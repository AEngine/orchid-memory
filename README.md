Orchid Memory
====
Work with Key-Value storage by user-friendly interface.

#### Requirements
* Orchid Framework
* PHP >= 7.0

#### Supporting
* Memcache
* Redis (coming soon)

#### Installation
Run the following command in the root directory of your web project:
  
> `composer require aengine/orchid-memory`

### Usage
Connect to the server  
Note: by default connect to Memcache
```php
Mem::setup([
    [
        'host'    => 'localhost',
        'port'    => '11211',
        'timeout' => 10,
    ],
]);
```

Write data to storage
```php
Mem::set('foo', 'bar');
```

Read data form storage
```php
Mem::get('foo');

// -- or --

Mem::get('foo', function () {
    // some action, e.g. just return string
    return 'baz';
});
```

#### Tags

```php
// set few rows
Mem::set('cat:0', 'Kiki', 3600, 'animal');
Mem::set('cat:1', 'Lucky', 3600, 'animal');
Mem::set('dog:0', 'Max', 3600, 'animal');
Mem::set('cat:2', 'Simon', 3600, 'animal');
Mem::set('dog:1', 'Duke', 3600, 'animal');
Mem::set('cat:3', 'Rocky', 3600, 'animal');
Mem::set('dog:2', 'Romeo', 3600, 'animal');

// get data as array
$animal = Mem::getByTag('animal');

// remove data
Mem::deleteByTag('animal');
```
