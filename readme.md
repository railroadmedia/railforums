# Railforums

## Install
With composer command
``` composer require railroad/railforums:1.0.4 ```

## Configure
In {application_dir}/config/railforums.php add
```
'table_prefix' => 'forum_',

'tables' => [
  'categories' => 'categories',
  'threads' => 'threads',
  'thread_follows' => 'thread_follows',
  'thread_reads' => 'thread_reads',
  'posts' => 'posts',
  'post_likes' => 'post_likes',
  'post_reports' => 'post_reports',
  'post_replies' => 'post_replies',
  'search_indexes' => 'search_indexes'
],

'author_table_name' => 'usora_users',
'author_table_id_column_name' => 'id',
'author_table_display_name_column_name' => 'display_name',

'user_data_mapper_class' => \App\DataMappers\ForumsUserCloakDataMapper::class,

// this tunes up the full text search
'search' => [
  'high_value_multiplier' => 4, // threads titles matches
  'medium_value_multiplier' => 2, // post content matches
  'low_value_multiplier' => 1, // authod display name matches
],

// this configures permissions->columns calls
'role_abilities' => [
  'developer' => [ // role - main array keys - this is just an example, not currently used
    'update-threads' => [ // permission used and checked in some controller action - second (inner) level keys
      '*', // rule - third (inner) level value - this allows developer role to edit all columns, including id's
    ],
  ],
  'administrator' => [ // role - main array keys
    'update-threads' => [ // permission used and checked in some controller action - second (inner) level keys
      'except' => [ // rule type - third (inner) level keys
        'id' // rule value - forth (inner) level value
      ]
    ],
    'update-posts' => [
      'except' => [
        'id'
      ]
    ]
  ],
  'moderator' => [ // role - main array keys
    'update-threads' => [
      'except' => [
        'id'
      ]
    ],
    'update-posts' => [
      'except' => [
        'id'
      ]
    ]
  ],
  'user' => [ // role - main array keys
    'update-threads' => [
      'only' => [ // rule type
        'title' // rule value
      ]
    ],
    'update-posts' => [
      'only' => [
        'content'
      ]
    ]
  ]
]
```

## API Reference

### Store Thread - forms controller

```
PUT /thread/store
```

#### Request Example

```
<form action="/thread/store" method="POST">
  <input name="_method" type="hidden" value="PUT">

  <input name="title" type="text" required maxlength="255">
  <input name="first_post_content" type="text">
  <input name="category_id" type="number" required>
</form>
```

#### Request Parameters

| path\|query\|body | key                | required | default            | description\|notes             |
| ----------------- | ------------------ | -------- | ------------------ | ------------------------------ |
| body              | _method            | yes      |                    | Set HTTP method verb to PUT    |
| body              | title              | yes      |                    | Thread title                   |
| body              | first_post_content | yes      |                    | First post content             |
| body              | redirect           | no       | redirect()->back() | The URI to redirect on success |

#### Response Example

``` 302 ```
Redirects to previous url or to path passed in with redirect param.\
Session has either success key set to true or error bag with validation errors.\
``` 404 ``` when user does not have permission to create threads

### Update Thread - forms controller

```
PATCH /thread/update/{id}
```

#### Request Example

```
<form action="/thread/update/{id}" method="POST">
  <input name="_method" type="hidden" value="PATCH">

  <input name="title" type="text" required maxlength="255">
</form>
```

#### Request Parameters

| path\|query\|body | key      | required | default            | description\|notes             |
| ----------------- | -------- | -------- | ------------------ | ------------------------------ |
| path              | {id}     | yes      |                    | The thread id to update        |
| body              | _method  | yes      |                    | Set HTTP method verb to PATCH  |
| body              | title    | yes      |                    | New thread title               |
| body              | redirect | no       | redirect()->back() | The URI to redirect on success |

#### Response Example

``` 302 ```
Redirects to previous url or to path passed in with redirect param.\
Session has either success key set to true or error bag with validation errors.\
``` 404 ``` when user does not have permission to update threads\
``` 404 ``` when specified thread does not exist

### Mark Thread as read - forms controller

```
PUT /thread/read/{id}
```

#### Request Example

```
<form action="/thread/read/{id}" method="POST">
  <input name="_method" type="hidden" value="PUT">
</form>
```

#### Request Parameters

| path\|query\|body | key      | required | default            | description\|notes             |
| ----------------- | -------- | -------- | ------------------ | ------------------------------ |
| path              | {id}     | yes      |                    | The thread id to mark as read  |
| body              | _method  | yes      |                    | Set HTTP method verb to PUT    |
| body              | redirect | no       | redirect()->back() | The URI to redirect on success |

#### Response Example

``` 302 ```
Redirects to previous url or to path passed in with redirect param.\
Session has success key set to true\
``` 404 ``` when user does not have permission to read threads\
``` 404 ``` when specified thread does not exist

### Follow Thread - forms controller

```
PUT /thread/follow/{id}
```

#### Request Example

```
<form action="/thread/follow/{id}" method="POST">
  <input name="_method" type="hidden" value="PUT">
</form>
```

#### Request Parameters

| path\|query\|body | key      | required | default            | description\|notes             |
| ----------------- | -------- | -------- | ------------------ | ------------------------------ |
| path              | {id}     | yes      |                    | The thread id to follow        |
| body              | _method  | yes      |                    | Set HTTP method verb to PUT    |
| body              | redirect | no       | redirect()->back() | The URI to redirect on success |

#### Response Example

``` 302 ```
Redirects to previous url or to path passed in with redirect param.\
Session has success key set to true.\
``` 404 ``` when user does not have permission to follow threads\
``` 404 ``` when specified thread does not exist

### Unfollow Thread - forms controller

```
DELETE /thread/unfollow/{id}
```

#### Request Example

```
<form action="/thread/unfollow/{id}" method="POST">
  <input name="_method" type="hidden" value="DELETE">
</form>
```

#### Request Parameters

| path\|query\|body | key      | required | default            | description\|notes             |
| ----------------- | -------- | -------- | ------------------ | ------------------------------ |
| path              | {id}     | yes      |                    | The thread id to unfollow      |
| body              | _method  | yes      |                    | Set HTTP method verb to DELETE |
| body              | redirect | no       | redirect()->back() | The URI to redirect on success |

#### Response Example

``` 302 ```
Redirects to previous url or to path passed in with redirect param.\
Session has success key set to true\
``` 404 ``` when user does not have permission to follow threads\
``` 404 ``` when specified thread does not exist

### Store Thread - JSON controller

```
PUT /forums/thread/store
```

#### Request Example

```
var title = 'Consectetur officia hic possimus iure et minima minima ut.';
var firstPostContent = 'sed accusamus dolorem ut';

$.ajax({
    url: '/forums/thread/store',
    type: 'put',
    data: {title: title, first_post_content: firstPostContent},
    dataType: 'json',
    success: function(response) {
        // handle success
    },
    error: function(response) {
        // handle error
    }
});
```

#### Request Parameters

| path\|query\|body | key                | required | default | description\|notes                             |
| ----------------- | ------------------ | -------- | ------- | ---------------------------------------------- |
| body              | title              | yes      |         | Thread title                                   |
| body              | first_post_content | yes      |         | First post content                             |
| body              | category_id        | yes      |         | The category (topic) id this thread belongs to |

#### Response Example

`200 OK`
```
{
  "id": 1,
  "category_id": 1,
  "author_id": 1,
  "title": "Consectetur officia hic possimus iure et minima minima ut.",
  "slug": "consectetur-officia-hic-possimus-iure-et-minima-minima-ut",
  "pinned": 0,
  "locked": 0,
  "state": "published",
  "post_count": 1,
  "published_on": "2018-07-05 09:29:54",
  "created_at": "2018-07-05 09:29:54",
  "updated_at": "2018-07-05 09:29:54",
  "deleted_at": null,
  "last_post_published_on": "2018-07-05 09:29:54",
  "last_post_id": 1,
  "last_post_user_id": 1,
  "last_post_user_display_name": "lee.wehner1",
  "is_read": 0,
  "is_followed": 0
}
```
```404``` when user does not have permission to create threads\
```402``` validation errors
```
{
  "status": "error",
  "code": 422,
  "total_results": 0,
  "results": [],
  "errors": [
    {
      "source": "title",
      "detail": "The title field is required."
    },
    {
      "source": "first_post_content",
      "detail": "The first post content field is required."
    },
    {
      "source": "category_id",
      "detail": "The category id field is required."
    }
  ]
}
```

### Update Thread - JSON controller

```
PATCH /forums/thread/update/{id}
```

#### Request Example

```
var title = 'Consectetur officia hic possimus iure et minima minima ut.';
var threadId = 1;

$.ajax({
    url: '/forums/thread/update/' + threadId,
    type: 'patch',
    data: {title: title},
    dataType: 'json',
    success: function(response) {
        // handle success
    },
    error: function(response) {
        // handle error
    }
});
```

#### Request Parameters

| path\|query\|body | key    | required | default | description\|notes      |
| ----------------- | ------ | -------- | ------- | ----------------------- |
| path              | {id}   | yes      |         | The thread id to update |
| body              | title  | yes      |         | New thread title        |

#### Response Example

` 200 OK`
```
{
  "id": 1,
  "category_id": 1,
  "author_id": 1,
  "title": "Consectetur officia hic possimus iure et minima minima ut.",
  "slug": "consectetur-officia-hic-possimus-iure-et-minima-minima-ut",
  "pinned": 0,
  "locked": 0,
  "state": "published",
  "post_count": 1,
  "published_on": "2018-07-05 09:29:54",
  "created_at": "2018-07-05 09:29:54",
  "updated_at": "2018-07-05 09:29:54",
  "deleted_at": null,
  "last_post_published_on": "2018-07-05 09:29:54",
  "last_post_id": 1,
  "last_post_user_id": 1,
  "last_post_user_display_name": "lee.wehner1",
  "is_read": 0,
  "is_followed": 0
}
```
```404``` when user does not have permission to update the thread\
```404``` when the specified thread id is not found\
```402``` validation error
```
{
  "status": "error",
  "code": 422,
  "total_results": 0,
  "results": [],
  "errors": [
    {
      "source": "title",
      "detail": "The title must be at least 1 characters."
    }
  ]
}
```

### Mark Thread as read - JSON controller

```
PUT /forums/thread/read/{id}
```

#### Request Example

```
var threadId = 1;

$.ajax({
    url: '/forums/thread/read/' + threadId,
    type: 'put',
    data: {},
    dataType: 'json',
    success: function(response) {
        // handle success
    },
    error: function(response) {
        // handle error
    }
});
```

#### Request Parameters

| path\|query\|body | key  | required | default | description\|notes             |
| ----------------- | ---- | -------- | ------- | ------------------------------ |
| path              | {id} | yes      |         | The thread id to mark as read  |

#### Response Example

` 200 OK`
```
{
  "id": 1,
  "thread_id": 1,
  "reader_id": 1,
  "read_on": "2018-07-05 11:58:39",
  "created_at": "2018-07-05 11:58:39",
  "updated_at": "2018-07-05 11:58:39"
}
```
``` 404 ``` when user does not have permission to read threads\
``` 404 ``` when specified thread does not exist

### Follow Thread - JSON controller

```
PUT /forums/thread/follow/{id}
```

#### Request Example

```
var threadId = 1;

$.ajax({
    url: '/forums/thread/follow/' + threadId,
    type: 'put',
    data: {},
    dataType: 'json',
    success: function(response) {
        // handle success
    },
    error: function(response) {
        // handle error
    }
});
```

#### Request Parameters

| path\|query\|body | key  | required | default | description\|notes      |
| ----------------- | ---- | -------- | ------- | ----------------------- |
| path              | {id} | yes      |         | The thread id to follow |

#### Response Example

` 200 OK`
```
{
  "id": 1,
  "thread_id": 1,
  "reader_id": 1,
  "read_on": "2018-07-05 11:58:39",
  "created_at": "2018-07-05 11:58:39",
  "updated_at": "2018-07-05 11:58:39"
}
```
``` 404 ``` when user does not have permission to follow threads\
``` 404 ``` when specified thread does not exist

### Unfollow Thread - JSON controller

```
DELETE /forums/thread/unfollow/{id}
```

#### Request Example

```
var threadId = 1;

$.ajax({
    url: '/forums/thread/unfollow/' + threadId,
    type: 'put',
    data: {},
    dataType: 'json',
    success: function(response) {
        // handle success
    },
    error: function(response) {
        // handle error
    }
});
```

#### Request Parameters

| path\|query\|body | key  | required | default | description\|notes        |
| ----------------- | ---- | -------- | ------- | ------------------------- |
| path              | {id} | yes      |         | The thread id to unfollow |

#### Response Example

``` 204 ``` No content\
``` 404 ``` when user does not have permission to follow threads\
``` 404 ``` when specified thread does not exist

### Index Thread - JSON controller

```
GET /forums/thread/index
```

#### Request Example

```
$.ajax({
    url: '/forums/thread/index',
    type: 'put',
    data: {amount: 2, page: 1, followed: true},
    dataType: 'json',
    success: function(response) {
        // handle success
    },
    error: function(response) {
        // handle error
    }
});
```

#### Request Parameters

| path\|query\|body | key          | required | default | description\|notes                           |
| ----------------- | ------------ | -------- | ------- | -------------------------------------------- |
| query             | amount       | no       | 10      | The amount of threads to return              |
| query             | page         | no       | 1       | The page of threads to return                |
| query             | category_ids | no       | null    | The category id (topic) of threads to return |
| query             | pinned       | no       | null    | Boolean, the type of threads to return       |
| query             | followed     | no       | null    | Boolean, the type of threads to return       |

#### Response Example

` 200 OK `
```
{
  "threads": [
    {
      "id": 20,
      "category_id": 1,
      "author_id": 1,
      "title": "Porro dolores saepe quos architecto aliquam rerum odit deleniti ea iste et soluta vitae et delectus rerum.",
      "slug": "eos-et-sunt-voluptas-dolore",
      "pinned": 0,
      "locked": 0,
      "state": "published",
      "post_count": 0,
      "published_on": "2004-12-07 07:06:39",
      "created_at": null,
      "updated_at": null,
      "deleted_at": null,
      "last_post_published_on": null,
      "last_post_id": null,
      "last_post_user_id": null,
      "last_post_user_display_name": null,
      "is_read": 0,
      "is_followed": 0
    },
    {
      "id": 19,
      "category_id": 1,
      "author_id": 1,
      "title": "Quaerat totam molestias quam tempore eum ducimus quam ratione aspernatur non autem ea quia quia debitis laboriosam omnis amet quam.",
      "slug": "ratione-est-qui-sed-quas",
      "pinned": 0,
      "locked": 0,
      "state": "published",
      "post_count": 0,
      "published_on": "2016-02-15 16:05:04",
      "created_at": null,
      "updated_at": null,
      "deleted_at": null,
      "last_post_published_on": null,
      "last_post_id": null,
      "last_post_user_id": null,
      "last_post_user_display_name": null,
      "is_read": 0,
      "is_followed": 0
    }
  ],
  "count": 20
}
```
``` 404 ``` when user does not have permission to index threads

### Show Thread - JSON controller

```
GET /forums/thread/show/{id}
```

#### Request Example

```
var threadId = 1;

$.ajax({
    url: '/forums/thread/show/' + threadId,
    type: 'get',
    data: {},
    dataType: 'json',
    success: function(response) {
        // handle success
    },
    error: function(response) {
        // handle error
    }
});
```

#### Request Parameters

| path\|query\|body | key  | required | default | description\|notes    |
| ----------------- | ---- | -------- | ------- | --------------------- |
| path              | {id} | yes      |         | The thread id to show |

#### Response Example

` 200 OK `
```
{
  "id": 1,
  "category_id": 1,
  "author_id": 1,
  "title": "Atque unde corporis et esse consequatur consequuntur rerum qui aut id dignissimos nihil veniam at placeat sed ullam error voluptatem eligendi nihil ipsa.",
  "slug": "tempore-consequatur-quo-itaque-aspernatur",
  "pinned": 0,
  "locked": 0,
  "state": "published",
  "post_count": 1,
  "published_on": "1976-09-05 12:33:17",
  "created_at": null,
  "updated_at": null,
  "deleted_at": null,
  "last_post_published_on": "1993-12-21 04:13:54",
  "last_post_id": 1,
  "last_post_user_id": 1,
  "last_post_user_display_name": "maximillian228",
  "is_read": 1,
  "is_followed": 1
}
```
``` 404 ``` when user does not have permission to show threads\
``` 404 ``` when specified thread does not exist

### Delete Thread - JSON controller

```
DELETE /forums/thread/delete/{id}
```

#### Request Example

```
var threadId = 1;

$.ajax({
    url: '/forums/thread/delete/' + threadId,
    type: 'put',
    data: {},
    dataType: 'json',
    success: function(response) {
        // handle success
    },
    error: function(response) {
        // handle error
    }
});
```

#### Request Parameters

| path\|query\|body | key  | required | default | description\|notes      |
| ----------------- | ---- | -------- | ------- | ----------------------- |
| path              | {id} | yes      |         | The thread id to delete |

#### Response Example

``` 204 ``` No content\
``` 404 ``` when user does not have permission to delete threads\
``` 404 ``` when specified thread does not exist

### Store Post - forms controller

```
PUT /post/store
```

#### Request Example

```
<form action="/post/store" method="POST">
  <input name="_method" type="hidden" value="PUT">

  <input name="content" type="text" required>
  <input name="thread_id" type="number" required>
  <input name="prompting_post_id" type="number">
</form>
```

#### Request Parameters

| path\|query\|body | key               | required | default            | description\|notes                  |
| ----------------- | ----------------- | -------- | ------------------ | ----------------------------------- |
| body              | _method           | yes      |                    | Set HTTP method verb to PUT         |
| body              | content           | yes      |                    | Post content                        |
| body              | thread_id         | yes      |                    | Thread id that this post belongs to |
| body              | prompting_post_id | yes      |                    |                                     |
| body              | redirect          | no       | redirect()->back() | The URI to redirect on success      |

#### Response Example

``` 302 ```
Redirects to previous url or to path passed in with redirect param.\
Session has either success key set to true or error bag with validation errors.\
``` 404 ``` when user does not have permission to create posts

### Update Post - forms controller

```
PATCH /post/update/{id}
```

#### Request Example

```
<form action="/post/update/{id}" method="POST">
  <input name="_method" type="hidden" value="PATCH">

  <input name="content" type="text">
</form>
```

#### Request Parameters

| path\|query\|body | key      | required | default            | description\|notes             |
| ----------------- | -------- | -------- | ------------------ | ------------------------------ |
| path              | {id}     | yes      |                    | The post id to update          |
| body              | _method  | yes      |                    | Set HTTP method verb to PATCH  |
| body              | content  | no       |                    | New post content               |
| body              | redirect | no       | redirect()->back() | The URI to redirect on success |

#### Response Example

``` 302 ```
Redirects to previous url or to path passed in with redirect param.\
Session has either success key set to true or error bag with validation errors.\
``` 404 ``` when user does not have permission to update posts\
``` 404 ``` when specified post does not exist

### Like Post - forms controller

```
PUT /post/like/{id}
```

#### Request Example

```
<form action="/post/like/{id}" method="POST">
  <input name="_method" type="hidden" value="PUT">
</form>
```

#### Request Parameters

| path\|query\|body | key      | required | default            | description\|notes             |
| ----------------- | -------- | -------- | ------------------ | ------------------------------ |
| path              | {id}     | yes      |                    | The post id to follow          |
| body              | _method  | yes      |                    | Set HTTP method verb to PUT    |
| body              | redirect | no       | redirect()->back() | The URI to redirect on success |

#### Response Example

``` 302 ```
Redirects to previous url or to path passed in with redirect param.\
Session has success key set to true.\
``` 404 ``` when user does not have permission to like posts\
``` 404 ``` when specified post does not exist

### Unlike Post - forms controller

```
DELETE /post/unlike/{id}
```

#### Request Example

```
<form action="/post/unlike/{id}" method="POST">
  <input name="_method" type="hidden" value="DELETE">
</form>
```

#### Request Parameters

| path\|query\|body | key      | required | default            | description\|notes             |
| ----------------- | -------- | -------- | ------------------ | ------------------------------ |
| path              | {id}     | yes      |                    | The post id to unlike          |
| body              | _method  | yes      |                    | Set HTTP method verb to DELETE |
| body              | redirect | no       | redirect()->back() | The URI to redirect on success |

#### Response Example

``` 302 ```
Redirects to previous url or to path passed in with redirect param.\
Session has success key set to true\
``` 404 ``` when user does not have permission to like posts\
``` 404 ``` when specified post does not exist

### Store Post - JSON controller

```
PUT /forums/post/store
```

#### Request Example

```
var content = 'Temporibus provident modi quo.';
var threadId = 1;

$.ajax({
    url: '/forums/post/store',
    type: 'put',
    data: {content: content, thread_id: threadId},
    dataType: 'json',
    success: function(response) {
        // handle success
    },
    error: function(response) {
        // handle error
    }
});
```

#### Request Parameters

| path\|query\|body | key               | required | default | description\|notes                 |
| ----------------- | ----------------- | -------- | ------- | ---------------------------------- |
| body              | content           | yes      |         | Post content                       |
| body              | thread_id         | yes      |         | The thread id this post belongs to |
| body              | prompting_post_id | no       |         |                                    |

#### Response Example

`200 OK`
```
{
  "id": 1,
  "thread_id": 1,
  "author_id": 1,
  "prompting_post_id": null,
  "content": "Temporibus provident modi quo.",
  "state": "published",
  "published_on": "2018-07-05 13:37:02",
  "edited_on": null,
  "created_at": "2018-07-05 13:37:02",
  "updated_at": "2018-07-05 13:37:02",
  "deleted_at": null
}
```
```404``` when user does not have permission to create posts\
```422``` validation errors
```
{
  "status": "error",
  "code": 422,
  "total_results": 0,
  "results": [],
  "errors": [
    {
      "source": "content",
      "detail": "The content field is required."
    },
    {
      "source": "thread_id",
      "detail": "The thread id field is required."
    }
  ]
}
```
