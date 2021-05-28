# Laravel Many to Many Self Relationship

[![Version](https://img.shields.io/badge/Version-2.0-brightgreen)](https://packagist.org/packages/kingmaker/laravel-many-to-many-self-relationship#2.0.0)
[![Laravel](https://img.shields.io/badge/Laravel-6%2B-ff694b)](https://laravel.com/)
[![Tests](https://img.shields.io/badge/Tests-passing-green)](tests)
[![License](https://img.shields.io/badge/License-MIT-blue)](https://opensource.org/licenses/MIT)

This package includes an extension to the `belongsToMany` Relationship of the Laravel
allowing **two-way association** between the same model with a single Entry on the pivot table.

## Installation
The package can be installed via Composer.

```bash
composer install kingmaker/laravel-many-to-many-self-relationship
```

## Version

This package is available on specific Laravel versions.

| Package Version | Laravel Version |
| --------------- | --------------- |
| 1.x             | 6.20+ or 7.29+  |
| 2.0.x           | 8.17 - 8.34     |
| 2.1+            | 8.35+           |

## Usage
Include the `HasBelongsToManySelfRelation` trait in the **Model class** and 
define the relation method as follows:

```php
use Illuminate\Database\Eloquent\Model;
use Kingmaker\Illuminate\Eloquent\Relations\HasBelongsToManySelfRelation;

class Post extends Model {

    use HasBelongsToManySelfRelation;

    public function relatedPosts()
    {
        return $this->belongsToManySelf('related_posts', 'post1', 'post2');
    }
}
```

It is that simple and has all the Methods available on the `belongsToMany()`.
The Related Posts can be accessed like any normal relation.

```php
$post = Post::first();
$post->relatedPosts; // returns Collection of Related Posts
```

> This is an extension over the native `BelongsToMany` class provided by the Eloquent.

## Example
Consider there are 'posts' table and 'related_posts' table. 
The Post represent  Post in the blog and it can have related posts similar to them.

```markdown
posts:
| id | title | body |
+----+-------+------+
| 1  | a     | ...  |
| 2  | b     | ...  |
| 3  | c     | ...  |
| 4  | d     | ...  |
+----+-------+------+

related_posts:
| id | post1 | post2 |
+----+-------+-------+
| 1  | 1     | 3     |
| 2  | 1     | 4     |
| 3  | 2     | 1     |
| 4  | 2     | 3     |
| 5  | 3     | 4     |
| 6  | 4     | 2     |
+----+-------+-------+
```

The `belongsToManySelf` relation provides **two-way association** when calling the Relation.

```php
Post::find(1)->relatedPosts; // returns Posts with id 2, 3, 4
Post::find(2)->relatedPosts; // returns Posts with id 1, 3, 4
Post::find(3)->relatedPosts; // returns Posts with id 1, 2, 4
Post::find(4)->relatedPosts; // returns Posts with id 1, 2, 3
```

## Caution
There is a possibility for the returning of _duplicate related object_ 
if the entities are related by 2rows on the Pivot table.

## Use Cases
This **two-way associated** Many-to-Many relationship can be used in few peculiar situations.
This relation doesn't suit all the Scenarios.

### a) Related Posts / Products
Consider a blog or shopping website, where we have _post / product_ related to each other.
When the related Entity is matched via `BelongsToMany` relationship, the association of one Post/Product as related to
the other post/product, on fetching the _relatedPost_ or _relatedProduct_ from the first Object will yield the second, 
but the inverse is not true (_ie, getting first post/product as related post/product from the second_)

### b) Friend list in social media
In Social Media, the concept of Users being friended to one another can be easily achieved using this relation (not followers & following, just mutual friends).
Once a Users add another User as friends, we can attach the User and doesn't need to care about the attachment of reverse while using this relationship.

> **Note**: The Concept of _followers_ and _following_ has to use the Laravel's `BelongsToMany` relation.
> This relation is for _friends_ like in Facebook.

### c) Messages in a Chat
The Message in a Chat application, where a message is directed from a User to another User.
This relation lets you retrieve all the Users that have messaged a particular User. 
> **Note**: There may be possibility of duplicates in this case

## API Reference
The trait `HasBelongsToManySelfRelation` adds the method `belongsToManySelf` to your Model.

```php
    /**
     * create the BelongsToManySelf relation on the same Model via a pivot table
     *
     * @param string $table Pivot table name
     * @param string $pivotKey1 Pivot table foreign key 1
     * @param string $pivotKey2 Pivot table foreign key 2
     * @param string|null $relatedKey Related key on the parent table
     * @param string|null $relation Relation name
     * @return BelongsToManySelf
     */
    public function belongsToManySelf(string $table, string $pivotKey1, string $pivotKey2, $relatedKey = null, $relation = null)
```

The `BelongsToManySelf` is a concrete/child/sub-class of the Eloquent `BelongsToMany` class.
So, All the methods available on the `BelongsToMany` is also available on this Relation.

## Known Issues
The following Issues / Problems were found in this package or the underlying Database Engine.

- the `has` and `whereHas` constraint will not work in **MySQL < v8.0.14**

## Tips

- To improve the performance of the relation, create **two Pivot Table indexes** such as `(pivot_key1, pivot_key2)` and `(pivot_key2, pivot_key1)`

## Contributing

Contributions are always welcome!

Feel free to open Issues and submit **Pull Requests**
