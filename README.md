Orchid Memory
====
Work with Key-Value storage by user-friendly interface.

#### Requirements
* PHP >= 7.0

#### Supporting
* Memcache
* Redis

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

#### Get or Set Multiple (like a PSR-16)

```php
// set rows
Mem::setMultiple([
    'cat:0' => 'Kiki',
    'cat:1' => 'Lucky',
    'dog:0' => 'Bucks',
    'cat:2' => 'Simon',
    'dog:1' => 'Eugene',
    'cat:3' => 'Rocky',
], 3600, 'animal');

// get data
$animals = Mem::getMultiple(['cat:0', 'cat:1', 'dog:0', 'cat:2', 'dog:1', 'cat:3']);

// remove data
Mem::deleteMultiple(['cat:0', 'cat:1', 'dog:0', 'cat:2', 'dog:1', 'cat:3']);
```

#### Tags

```php
// set few rows
Mem::set('cat:0', 'Kiki', 3600, 'animal');
Mem::set('cat:1', 'Lucky', 3600, 'animal');
Mem::set('dog:0', 'Bucks', 3600, 'animal');
Mem::set('cat:2', 'Simon', 3600, 'animal');
Mem::set('dog:1', 'Eugene', 3600, 'animal');
Mem::set('cat:3', 'Rocky', 3600, 'animal');

// get data as array
$animal = Mem::getByTag('animal');

// remove data
Mem::deleteByTag('animal');
```

#### Contributing
Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

#### License
The Orchid Memory is licensed under the MIT license. See [License File](LICENSE.md) for more information.
