<?php

namespace App\Controllers;

use Framework\Database;
use Framework\Validation;
use Framework\Session;
use Framework\Authorization;

class ListingController
{
    protected $db;

    public function __construct()
    {
        $config = require basePath('config/db.php');
        $this->db = new Database($config);
    }

    public function index()
    {
        $listings = $this->db->query('SELECT * FROM listings ORDER BY created_at DESC LIMIT 6')->fetchAll();

        loadView('listings/index', [
            'listings' => $listings
        ]);
    }

    public function create()
    {
        loadView('listings/create');
    }

    public function show($params)
    {
        $id = $params['id'] ?? '';
        $params = ['id' => $id];

        $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', $params)->fetch();

        if (!$listing) {
            ErrorController::notFound('Listing not found');
            return;
        }

        loadView('listings/show', [
            'listing' => $listing
        ]);
    }

    /**
     * Store data in database
     *
     * @return void
     */
    public function store()
    {
        $allowedFields = ['title', 'description', 'salary', 'tags', 'company', 'address',
            'city', 'state', 'phone', 'email', 'requirements', 'benefits'];

        $newListingData = array_intersect_key($_POST, array_flip($allowedFields));
        $newListingData['user_id'] = Session::get('user')['id'];
        $newListingData = array_map('sanitize', $newListingData);

        $requiredFields = ['title', 'description', 'email', 'city', 'state', 'salary'];
        $errors = [];

        foreach ($requiredFields as $field) {
            if (empty($newListingData[$field]) || !Validation::string($newListingData[$field])) {
                $errors[$field] = ucfirst($field) . ' is required';
            }
        }

        if (!empty($errors)) {
            loadView('listings/create', [
                'errors' => $errors,
                'listing' => $newListingData
            ]);
            return;
        }

        $fields = array_keys($newListingData);
        $fieldList = implode(', ', $fields);
        $placeholders = implode(', ', array_map(fn ($field) => ':' . $field, $fields));

        $query = "INSERT INTO listings ({$fieldList}) VALUES ({$placeholders})";
        $this->db->query($query, $newListingData);

        Session::setFlashMessage('success_message', 'Listing created successfully');
        redirect(url('listings'));
    }

    /**
     * Delete listing
     *
     * @param array $params
     * @return void
     */
    public function destroy($params)
    {
        $id = $params['id'] ?? '';
        $params = ['id' => $id];

        $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', $params)->fetch();

        if (!$listing) {
            ErrorController::notFound('Listing not found');
            return;
        }

        if (!Authorization::isOwner($listing->user_id)) {
            Session::setFlashMessage('error_message', 'You are Unauthorized to delete this listing');
            return redirect(url('listings/' . $listing->id));
        }

        $this->db->query('DELETE FROM listings WHERE id = :id', $params);
        Session::setFlashMessage('success_message', 'Listing deleted successfully');
        redirect(url('listings'));
    }

    /**
     * Show edit form
     *
     * @param array $params
     * @return void
     */
    public function edit($params)
    {
        $id = $params['id'] ?? '';
        $params = ['id' => $id];

        // Fetch the listing from database
        $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', $params)->fetch();

        if (!$listing) {
            ErrorController::notFound('Listing not found');
            return;
        }

        // Check if user is owner
        if (!Authorization::isOwner($listing->user_id)) {
            Session::setFlashMessage('error_message', 'You are unauthorized to edit this listing');
            redirect(url('listings/' . $listing->id));
            return;
        }

        // Load edit view with listing data
        loadView('listings/edit', [
            'listing' => $listing,
            'errors' => Session::get('errors') ?? []
        ]);
        
        // Clear errors after displaying
        Session::clear('errors');
    }

    /**
     * Update listing
     *
     * @param array $params
     * @return void
     */
    public function update($params)
    {
        $id = $params['id'] ?? '';
        $params = ['id' => $id];

        $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', $params)->fetch();

        if (!$listing) {
            ErrorController::notFound('Listing not found');
            return;
        }

        if (!Authorization::isOwner($listing->user_id)) {
            Session::setFlashMessage('error_message', 'You are unauthorized to update this listing');
            return redirect(url('listings/' . $listing->id));
        }

        $allowedFields = ['title', 'description', 'salary', 'tags', 'company', 'address',
            'city', 'state', 'phone', 'email', 'requirements', 'benefits'];

        $updatedValues = array_intersect_key($_POST, array_flip($allowedFields));
        $updatedValues = array_map('sanitize', $updatedValues);

        $requiredFields = ['title', 'description', 'email', 'city', 'state'];
        $errors = [];

        foreach ($requiredFields as $field) {
            if (empty($updatedValues[$field]) || !Validation::string($updatedValues[$field])) {
                $errors[$field] = ucfirst($field) . ' is required';
            }
        }

        if (!empty($errors)) {
            // Store errors in session and reload edit form
            Session::set('errors', $errors);
            redirect(url('listings/' . $id . '/edit'));
            return;
        }

        $updateFields = [];
        foreach (array_keys($updatedValues) as $field) {
            $updateFields[] = "{$field} = :{$field}";
        }

        $updateFields = implode(', ', $updateFields);
        $updatedValues['id'] = $id;

        $updateQuery = "UPDATE listings SET {$updateFields} WHERE id = :id";
        $this->db->query($updateQuery, $updatedValues);

        Session::setFlashMessage('success_message', 'Listing updated successfully');
        redirect(url('listings/' . $id));
    }

    /**
     * Search listing by keyword/location
     *
     * @return void
     */
    public function search()
    {
        $keywords = isset($_GET['keywords']) ? trim($_GET['keywords']) : '';
        $location = isset($_GET['location']) ? trim($_GET['location']) : '';

        $query = "SELECT * FROM listings WHERE (title LIKE :keywords OR description LIKE :keywords OR tags LIKE :keywords OR company LIKE :keywords) AND (city LIKE :location OR state LIKE :location) ORDER BY created_at DESC";

        $params = ['keywords' => "%{$keywords}%", 'location' => "%{$location}%"];

        $listings = $this->db->query($query, $params)->fetchAll();

        loadView('listings/index', [
            'listings' => $listings,
            'keywords' => $keywords,
            'location' => $location
        ]);
    }
}