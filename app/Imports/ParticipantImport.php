<?php

namespace App\Imports;

use App\Models\Participant;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ParticipantImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */


    public function model(array $row)
    {

        $guest = new Participant();

        if (isset($row['salutation'])) {
            $guest->salutation = $row['salutation'];
        }
        if (isset($row['first_name'])) {
            $guest->first_name = $row['first_name'];
        }
        if (isset($row['last_name'])) {
            $guest->last_name = $row['last_name'];
        }
        if (isset($row['email'])) {
            $guest->email = $row['email'];
        }

        if (isset($row['occupation'])) {
            $guest->occupation = $row['occupation'];
        }

        if (isset($row['phone'])) {
            $guest->phone = $row['phone'];
        }


        if (isset($row['reg_key'])) {
            $guest->reg_key = $row['reg_key'];
        }
        if (isset($row['auth_key'])) {
            $guest->auth_key = $row['auth_key'];
        }

        // $guest->confirmed = 1;

        // Optionally generate random keys if not provided
        if (!isset($row['reg_key'])) {
            $guest->reg_key = Str::random(16);
        }
        if (!isset($row['auth_key'])) {
            $guest->auth_key = Str::random(16);
        }

        $guest->save();
    }
}
