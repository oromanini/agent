<?php

namespace App\Builders;

use App\Models\User;

class UserBuilder implements Builder
{
    private User $user;

    public function __construct()
    {
        $this->user = new User();
        $this->user->permission = 'agent';
    }

    public function withName(string $name): static
    {
        $this->user->name = $name;
        return $this;
    }

    public function withEmail(string $email): static
    {
        $this->user->email = $email;
        return $this;
    }

    public function withPassword(string $password): static
    {
        $this->user->password = $password;
        return $this;
    }

    public function withPhoneNumber(string $phoneNumber): static
    {
        $this->user->phone_number = $phoneNumber;
        return $this;
    }

    public function withContract(string $contract): static
    {
        $this->user->contract = $contract;
        return $this;
    }

    public function withCity(int $city): static
    {
        $this->user->city = $city;
        return $this;
    }

    public function withPermission(string $permission): static
    {
        if (!in_array($permission, ['agent', 'controller', 'admin'])) {
            throw new \InvalidArgumentException("Invalid permission type.");
        }
        $this->user->permission = $permission;
        return $this;
    }

    public function withCpf(string $cpf): static
    {
        $this->user->cpf = $cpf;
        return $this;
    }

    public function withCnpj(string $cnpj): static
    {
        $this->user->cnpj = $cnpj;
        return $this;
    }

    public function withAscendant(string $ascendant): static
    {
        $this->user->ascendant = $ascendant;
        return $this;
    }

    public function build(): User
    {
        $this->user->save();
        return $this->user;
    }
}
