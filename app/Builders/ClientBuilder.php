<?php

namespace App\Builders;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class ClientBuilder implements Builder
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->uuid = Uuid::uuid4();
    }

    public function withNameOrCompanyName(string $name): static
    {
        $this->client->name = $name;
        return $this;
    }

    public function withType(string $type): static
    {
        $this->client->type = $type;
        return $this;
    }

    public function withCpfOrCnpj(string $document): static
    {
        $this->client->document = $document;
        return $this;
    }

    public function withEmail(string $email): static
    {
        $this->client->email = $email;
        return $this;
    }

    public function withPhoneNumber(string $phoneNumber): static
    {
        $this->client->phone_number = $phoneNumber;
        return $this;
    }

    public function withAgentId(User|Model $user): static
    {
        $this->client->agent_id = $user->id;
        return $this;
    }

    public function withOwnerDocument(string $ownerDocument): static
    {
        $this->client->owner_document = $ownerDocument;
        return $this;
    }

    public function withBirthdate(string $birthdate): static
    {
        $this->client->birthdate = $birthdate;
        return $this;
    }

    public function withAccountOwnerDocument(string $accountOwnerDocument): static
    {
        $this->client->account_owner_document = $accountOwnerDocument;
        return $this;
    }

    public function withDocument(string $document): static
    {
        $this->client->document = $document;
        return $this;
    }

    public function build(): Client
    {
        $this->client->save();
        return $this->client;
    }
}
