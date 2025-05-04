<?php

class AuthService {
    private array $users = [];
    private string $file = "users.json";

    public function __construct() {
        if (file_exists($this->file)) {
            $this->users = json_decode(file_get_contents($this->file), true) ?? [];
        }
    }

    private function save(): void {
        file_put_contents($this->file, json_encode($this->users, JSON_PRETTY_PRINT));
    }

    public function register(string $username, string $password): string {
        if (isset($this->users[$username])) {
            return "Utilizator deja existent.";
        }

        $this->users[$username] = password_hash($password, PASSWORD_DEFAULT);
        $this->save();
        return "Înregistrare cu succes.";
    }

    public function authenticate(string $username, string $password): bool {
        if (!isset($this->users[$username])) {
            return false;
        }

        return password_verify($password, $this->users[$username]);
    }
}
