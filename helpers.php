<?php 



function basePath($path = '') {
    return __DIR__ . '/' . $path;
}

function baseUrl()
{
    $basePath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));

    if ($basePath === '/' || $basePath === '\\') {
        return '';
    }

    return rtrim($basePath, '/');
}

function url($path = '') {
    $baseUrl = baseUrl();
    $path = ltrim($path, '/');

    if ($path === '') {
        return $baseUrl === '' ? '/' : $baseUrl . '/';
    }

    return $baseUrl === '' ? '/' . $path : $baseUrl . '/' . $path;
}

function loadView($name, $data = [])
{
    $viewPath = basePath("App/Controllers/Views/{$name}.view.php");

    if (file_exists($viewPath)) {
        extract($data);
        require $viewPath;
    } else {
        echo "View {$name} not found!";
    }
} 

function loadPartial($name, $data = []) {
    $partialPath = basePath("App/Controllers/Views/Partials/{$name}.php");

    if (!file_exists($partialPath) && strpos($name, '-') !== false) {
        $normalizedName = lcfirst(str_replace(' ', '', ucwords(str_replace('-', ' ', $name))));
        $partialPath = basePath("App/Controllers/Views/Partials/{$normalizedName}.php");
    }

    if (file_exists($partialPath)) {
        extract($data);
        require $partialPath;
    } else {
        echo "Partial '{$name}' not found.";
    }
}

function inspect($value) {
    echo '<pre>';
    var_dump($value);
    echo '</pre>';
}

function viewValue($item, $key)
{
    if (is_array($item)) {
        return $item[$key] ?? null;
    }

    if (is_object($item)) {
        return $item->$key ?? null;
    }

    return null;
}

function formatsalary($salary) {
    return '$' . number_format(floatval($salary));
}

   function inspectAndDie($value)
{
    echo '<pre>';
    die(var_dump($value));
    echo '</pre>';
}

/**
 * Sanitize data
 * 
 * @param string $dirty
 * @return strign
 */

function sanitize($dirty){
    return filter_var(trim($dirty), FILTER_SANITIZE_SPECIAL_CHARS);
}

/**
 * Redirect to a given URL
 * 
 * @param string $url
 * @return void
 */
function redirect($url) {
    header("Location: {$url}");
    exit;
}