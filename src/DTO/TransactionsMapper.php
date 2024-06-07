<?php

namespace App\DTO;

class TransactionsMapper {

    public function mapDto(string $inputString): TransactionsDTO|false
    {
        // todo we can add Detection of the input string and implement mapping from another logic (for example serializing)
        if (!json_validate($inputString))
            return false;

        $obj = json_decode($inputString);

        if (!$obj->bin || !$obj->amount || !$obj->currency)
            return false;

        return new TransactionsDTO(
            $obj->bin,
            $obj->amount,
            $obj->currency,
        );
    }
}
