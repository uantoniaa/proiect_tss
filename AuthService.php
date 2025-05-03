<?php

class AuthService {
    private array $users = []; // username => password (hashed)

    public function register(string $username, string $password): string {
        if (isset($this->users[$username])) {
            return "Utilizator deja existent.";
        }

        $this->users[$username] = password_hash($password, PASSWORD_DEFAULT);
        return "Înregistrare cu succes.";
    }

    public function authenticate(string $username, string $password): bool {
        if (!isset($this->users[$username])) {
            return false;
        }

        return password_verify($password, $this->users[$username]);
    }
}
