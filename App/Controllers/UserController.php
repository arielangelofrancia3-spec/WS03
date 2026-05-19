<?php 

namespace App\Controllers;

use Framework\Database;
use Framework\Validation;
use Framework\Session;

class UserController
{
    protected $db;

    public function __construct()
    {
        $config = require basePath('config/db.php');
        $this->db = new Database($config);
    }

    /**
     * Show Login Page
     */
    public function login()
    {
        loadView('users/login');
    }

    /**
     * Show Register Page
     */
    public function create()
    {
        loadView('users/create');
    }

    /**
     * Register User
     */
    public function store()
    {
        $allowedFields = [
            'name',
            'email',
            'city',
            'state',
            'password',
            'password_confirmation'
        ];

        $newUserData = array_intersect_key($_POST, array_flip($allowedFields));

        $newUserData = array_map('sanitize', $newUserData);

        $name = $newUserData['name'] ?? '';
        $email = $newUserData['email'] ?? '';
        $city = $newUserData['city'] ?? '';
        $state = $newUserData['state'] ?? '';
        $password = $newUserData['password'] ?? '';
        $confirmPassword = $newUserData['password_confirmation'] ?? '';

        $errors = [];

        // Validation
        if (empty($name)) {
            $errors['name'] = 'Name is required';
        }

        if (!Validation::email($email)) {
            $errors['email'] = 'Please enter a valid email';
        }

        if (!Validation::string($password, 6, 50)) {
            $errors['password'] = 'Password must be at least 6 characters';
        }

        if ($password !== $confirmPassword) {
            $errors['password_confirmation'] = 'Passwords do not match';
        }

        // Check if email already exists
        $params = ['email' => $email];

        $user = $this->db->query(
            'SELECT * FROM users WHERE email = :email',
            $params
        )->fetch();

        if ($user) {
            $errors['email'] = 'Email already exists';
        }

        // If errors exist
        if (!empty($errors)) {
            loadView('users/create', [
                'errors' => $errors,
                'user' => $newUserData
            ]);
            exit;
        }

        // Create user account
        $params = [
            'name' => $name,
            'email' => $email,
            'city' => $city,
            'state' => $state,
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ];

        $this->db->query(
            'INSERT INTO users (name, email, city, state, password)
             VALUES (:name, :email, :city, :state, :password)',
            $params
        );

        // Get new user ID
        $userId = $this->db->conn->lastInsertId();

        // Set user session
        Session::set('user', [
            'id' => $userId,
            'name' => $name,
            'email' => $email,
            'city' => $city,
            'state' => $state
        ]);

        redirect('/');
    }

    /**
     * Logout User
     */
    public function logout()
    {
        Session::clear('user');

        $params = session_get_cookie_params();

        setcookie(
            'PHPSESSID',
            '',
            time() - 86400,
            $params['path'],
            $params['domain']
        );

        redirect('/');
    }

    /**
     * Authenticate User
     */
    public function authenticate()
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $errors = [];

        // Validation
        if (!Validation::email($email)) {
            $errors['email'] = 'Please enter a valid email address';
        }

        if (!Validation::string($password, 6, 50)) {
            $errors['password'] = 'Password must be at least 6 characters';
        }

        // Check for validation errors
        if (!empty($errors)) {
            loadView('users/login', [
                'errors' => $errors
            ]);
            exit;
        }

        // Check if user exists
        $params = [
            'email' => $email
        ];

        $user = $this->db->query(
            'SELECT * FROM users WHERE email = :email',
            $params
        )->fetch();

        if (!$user) {
            $errors['email'] = 'Incorrect credentials';

            loadView('users/login', [
                'errors' => $errors
            ]);

            exit;
        }

        // Verify password
        if (!password_verify($password, $user['password'])) {
            $errors['password'] = 'Incorrect credentials';

            loadView('users/login', [
                'errors' => $errors
            ]);

            exit;
        }

        // Set session
        Session::set('user', [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'city' => $user['city'],
            'state' => $user['state']
        ]);

        redirect('/');
    }
}
