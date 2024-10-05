<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'document',
        'address_id',
        'phone',
        'email',
    ];

    protected $encryptable = [
        'name',
        'document',
        'phone',
        'email',
    ];

    protected $decryptable = [
        'name',
        'document',
        'phone',
        'email',
    ];

    public function serviceOrders()
    {
        return $this->hasMany(ServiceOrder::class);
    }

    public function address()
    {
        return $this->hasOne(ClientAddress::class, 'id', 'address_id');
    }

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

        if (in_array($key, $this->decryptable) && $value !== null) {
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
