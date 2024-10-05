<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientAddress extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'street',
        'number',
        'complement',
        'district',
        'city',
        'state',
        'zip_code',
    ];

    protected $encryptable = [
        'street',
        'number',
        'complement',
        'district',
        'city',
        'state',
        'zip_code',
    ];


    /**
     * Mutator para criptografar os campos especificados.
     *
     * @param  string  $value
     * @return string
     */
    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->encryptable)) {
            $value = encrypt($value);
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * Accessor para descriptografar os campos especificados.
     *
     * @param  string  $value
     * @return string
     */
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        if (in_array($key, $this->encryptable) && $value !== null) {
            $value = decrypt($value);
        }

        return $value;
    }

    public function toArray()
    {
        $array = parent::toArray();

        foreach ($this->encryptable as $key) {
            if (isset($array[$key])) {
                $array[$key] = decrypt($array[$key]);
            }
        }

        return $array;
    }
}
