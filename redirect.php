<?php

class Redirect
{
    private $records = [];
    function __construct(array $txt_records)
    {
        $this->records = $this->extract_txt_content($txt_records);
        $this->sort_records();
        [$matching_record, $matches] = $this->find_matching_record();
        $destination = $this->replace_placeholder($matching_record, $matches);
        $this->forward($destination, $matching_record->get_http_code());
    }

    private function forward(string $destination, int $http_code): void
    {
        header("Location: $destination", true, $http_code);
        header('Content-Type: text/plain');
        echo "Redirecting to $destination";
        exit;
    }

    private function replace_placeholder(Record $record, array $matches): string
    {
        $url = $record->get_destination();
        foreach ($matches as $key => $match) {
            $url = str_replace("{{$key}}", $match, $url);
        }
        return $url;
    }

    private function find_matching_record(): array
    {
        foreach ($this->records as &$record) {
            if (preg_match("/{$record->get_regex()}/", $_SERVER['REQUEST_URI'], $matches)) {
                return [$record, $matches];
            }
        }

        return [new Record('.* http://' . HOME . '/ 302'), []];
    }

    /**
     * @return Record[]
     */
    private function extract_txt_content(array $txt_records): array
    {
        $result = [];
        foreach ($txt_records as &$txt_record) {
            $result[] = new Record($txt_record['txt']);
        }
        return $result;
    }

    private function sort_records()
    {
        usort($this->records, function (Record $a, Record $b) {
            return - ($a->get_priority() <=> $b->get_priority());
        });
    }
}

class Record
{
    private $regex = '';
    private $destination = '';
    private $http_code = 301;
    private $priority = 0;

    function __construct($txt_record)
    {
        $txt_record = trim($txt_record);
        $splitted = explode(' ', $txt_record);
        $this->regex = $splitted[0];
        if (!isset($splitted[1]))
            return;
        $this->destination = $splitted[1];
        if (!isset($splitted[2]))
            return;
        if (in_array($splitted[2], ['301', '302']))
            $this->http_code = (int) $splitted[2];
        if (!isset($splitted[3]))
            return;
        if (ctype_digit($splitted[3]))
            $this->priority = (int) $splitted[3];
    }

    function get_regex(): string
    {
        return $this->regex;
    }

    function get_destination(): string
    {
        return $this->destination;
    }

    function get_http_code(): int
    {
        return $this->http_code;
    }

    function get_priority(): int
    {
        return $this->priority;
    }
}
