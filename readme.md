# Railforums

- [Railforums](#railforums)
  * [Install](#install)
  * [Configure](#configure)
  * [API Reference](#api-reference)
    + [Store Thread - forms controller](#store-thread---forms-controller)
      - [Permission required `create-threads`](#permission-required--create-threads-)
      - [Request Example](#request-example)
      - [Request Parameters](#request-parameters)
      - [Response Example](#response-example)
    + [Update Thread - forms controller](#update-thread---forms-controller)
      - [Permission required `update-threads`](#permission-required--update-threads-)
      - [Request Example](#request-example-1)
      - [Request Parameters](#request-parameters-1)
      - [Response Example](#response-example-1)
    + [Mark Thread as read - forms controller](#mark-thread-as-read---forms-controller)
      - [Permission required `read-threads`](#permission-required--read-threads-)
      - [Request Example](#request-example-2)
      - [Request Parameters](#request-parameters-2)
      - [Response Example](#response-example-2)
    + [Follow Thread - forms controller](#follow-thread---forms-controller)
      - [Permission required `follow-threads`](#permission-required--follow-threads-)
      - [Request Example](#request-example-3)
      - [Request Parameters](#request-parameters-3)
      - [Response Example](#response-example-3)
    + [Unfollow Thread - forms controller](#unfollow-thread---forms-controller)
      - [Permission required `follow-threads`](#permission-required--follow-threads--1)
      - [Request Example](#request-example-4)
      - [Request Parameters](#request-parameters-4)
      - [Response Example](#response-example-4)
    + [Delete Thread - form controller](#delete-thread---form-controller)
      - [Permission required `delete-threads`](#permission-required--delete-threads-)
      - [Request Example](#request-example-5)
      - [Request Parameters](#request-parameters-5)
      - [Response Example](#response-example-5)
    + [Store Thread - JSON controller](#store-thread---json-controller)
      - [Permission required `create-threads`](#permission-required--create-threads--1)
      - [Request Example](#request-example-6)
      - [Request Parameters](#request-parameters-6)
      - [Response Example](#response-example-6)
    + [Update Thread - JSON controller](#update-thread---json-controller)
      - [Permission required `update-threads`](#permission-required--update-threads--1)
      - [Request Example](#request-example-7)
      - [Request Parameters](#request-parameters-7)
      - [Response Example](#response-example-7)
    + [Mark Thread as read - JSON controller](#mark-thread-as-read---json-controller)
      - [Permission required `read-threads`](#permission-required--read-threads--1)
      - [Request Example](#request-example-8)
      - [Request Parameters](#request-parameters-8)
      - [Response Example](#response-example-8)
    + [Follow Thread - JSON controller](#follow-thread---json-controller)
      - [Permission required `follow-threads`](#permission-required--follow-threads--2)
      - [Request Example](#request-example-9)
      - [Request Parameters](#request-parameters-9)
      - [Response Example](#response-example-9)
    + [Unfollow Thread - JSON controller](#unfollow-thread---json-controller)
      - [Permission required `follow-threads`](#permission-required--follow-threads--3)
      - [Request Example](#request-example-10)
      - [Request Parameters](#request-parameters-10)
      - [Response Example](#response-example-10)
    + [Index Thread - JSON controller](#index-thread---json-controller)
      - [Permission required `index-threads`](#permission-required--index-threads-)
      - [Request Example](#request-example-11)
      - [Request Parameters](#request-parameters-11)
      - [Response Example](#response-example-11)
    + [Show Thread - JSON controller](#show-thread---json-controller)
      - [Permission required `show-threads`](#permission-required--show-threads-)
      - [Request Example](#request-example-12)
      - [Request Parameters](#request-parameters-12)
      - [Response Example](#response-example-12)
    + [Delete Thread - JSON controller](#delete-thread---json-controller)
      - [Permission required `delete-threads`](#permission-required--delete-threads--1)
      - [Request Example](#request-example-13)
      - [Request Parameters](#request-parameters-13)
      - [Response Example](#response-example-13)
    + [Store Post - forms controller](#store-post---forms-controller)
      - [Permission required `create-posts`](#permission-required--create-posts-)
      - [Request Example](#request-example-14)
      - [Request Parameters](#request-parameters-14)
      - [Response Example](#response-example-14)
    + [Update Post - forms controller](#update-post---forms-controller)
      - [Request Example](#request-example-15)
      - [Permission required `update-posts`](#permission-required--update-posts-)
      - [Request Parameters](#request-parameters-15)
      - [Response Example](#response-example-15)
    + [Like Post - forms controller](#like-post---forms-controller)
      - [Permission required `like-posts`](#permission-required--like-posts-)
      - [Request Example](#request-example-16)
      - [Request Parameters](#request-parameters-16)
      - [Response Example](#response-example-16)
    + [Unlike Post - forms controller](#unlike-post---forms-controller)
      - [Permission required `like-posts`](#permission-required--like-posts--1)
      - [Request Example](#request-example-17)
      - [Request Parameters](#request-parameters-17)
      - [Response Example](#response-example-17)
    + [Store Post - JSON controller](#store-post---json-controller)
      - [Permission required `create-posts`](#permission-required--create-posts--1)
      - [Request Example](#request-example-18)
      - [Request Parameters](#request-parameters-18)
      - [Response Example](#response-example-18)
    + [Update Post - JSON controller](#update-post---json-controller)
      - [Request Example](#request-example-19)
      - [Permission required `update-posts`](#permission-required--update-posts--1)
      - [Request Parameters](#request-parameters-19)
      - [Response Example](#response-example-19)
    + [Like Post - JSON controller](#like-post---json-controller)
      - [Permission required `like-posts`](#permission-required--like-posts--2)
      - [Request Example](#request-example-20)
      - [Request Parameters](#request-parameters-20)
      - [Response Example](#response-example-20)
    + [Unlike Post - JSON controller](#unlike-post---json-controller)
      - [Permission required `like-posts`](#permission-required--like-posts--3)
      - [Request Example](#request-example-21)
      - [Request Parameters](#request-parameters-21)
      - [Response Example](#response-example-21)
    + [Index Post - JSON controller](#index-post---json-controller)
      - [Permission required `index-posts`](#permission-required--index-posts-)
      - [Request Example](#request-example-22)
      - [Request Parameters](#request-parameters-22)
      - [Response Example](#response-example-22)
    + [Show Post - JSON controller](#show-post---json-controller)
      - [Permission required `show-posts`](#permission-required--show-posts-)
      - [Request Example](#request-example-23)
      - [Request Parameters](#request-parameters-23)
      - [Response Example](#response-example-23)
    + [Search Posts and/or Threads - JSON controller](#search-posts-and-or-threads---json-controller)
      - [Request Example](#request-example-24)
      - [Request Parameters](#request-parameters-24)
      - [Response Example](#response-example-24)
    + [Store Discussion - forms controller](#store-discussion---forms-controller)
      - [Permission required `create-discussions`](#permission-required--create-discussions-)
      - [Request Example](#request-example-25)
      - [Request Parameters](#request-parameters-25)
      - [Response Example](#response-example-25)
    + [Update Discussion - forms controller](#update-discussion---forms-controller)
      - [Permission required `update-discussions`](#permission-required--update-discussions-)
      - [Request Example](#request-example-26)
      - [Request Parameters](#request-parameters-26)
      - [Response Example](#response-example-26)
  * [Mobile endpoints](#mobile-endpoints)
    + [Index Discussions(Topics)](#index-discussions-topics-)
      - [Permission required `index-discussions`](#permission-required--index-discussions-)
      - [Request Example](#request-example-27)
      - [Request Parameters](#request-parameters-27)
      - [Response Example](#response-example-27)

<small><i><a href='http://ecotrust-canada.github.io/markdown-toc/'>Table of contents generated with markdown-toc</a></i></small>


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

#### Permission required `create-threads`

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

#### Permission required `update-threads`

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

#### Permission required `read-threads`

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

#### Permission required `follow-threads`

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

#### Permission required `follow-threads`

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

### Delete Thread - form controller

```
DELETE /thread/delete/{id}
```

#### Permission required `delete-threads`

#### Request Example

```
<form action="/thread/delete/{id}" method="POST">
  <input name="_method" type="hidden" value="DELETE">
</form>
```

#### Request Parameters

| path\|query\|body | key      | required | default            | description\|notes             |
| ----------------- | -------- | -------- | ------------------ | ------------------------------ |
| path              | {id}     | yes      |                    | The thread id to delete      |
| body              | _method  | yes      |                    | Set HTTP method verb to DELETE |
| body              | redirect | no       | redirect()->back() | The URI to redirect on success |

#### Response Example

``` 302 ```
Redirects to previous url or to path passed in with redirect param.\
Session has success key set to true\
``` 404 ``` when user does not have permission to delete threads\
``` 404 ``` when specified thread does not exist

### Store Thread - JSON controller

```
PUT /forums/thread/store
```

#### Permission required `create-threads`

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

#### Permission required `update-threads`

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

#### Permission required `read-threads`

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

#### Permission required `follow-threads`

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

#### Permission required `follow-threads`

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

#### Permission required `index-threads`

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

#### Permission required `show-threads`

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

#### Permission required `delete-threads`

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

#### Permission required `create-posts`

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

#### Permission required `update-posts`

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

#### Permission required `like-posts`

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

#### Permission required `like-posts`

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

#### Permission required `create-posts`

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

### Update Post - JSON controller

```
PATCH /post/update/{id}
```

#### Request Example

#### Permission required `update-posts`

```
var content = 'Nam sit delectus debitis consectetur.';

$.ajax({
    url: '/forums/post/update/{id}',
    type: 'patch',
    data: {content: content},
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

| path\|query\|body | key      | required | default | description\|notes    |
| ----------------- | -------- | -------- | ------- | --------------------- |
| path              | {id}     | yes      |         | The post id to update |
| body              | content  | yes      |         | New post content      |

#### Response Example

`200 OK`
```
{
  "id": 1,
  "thread_id": 1,
  "author_id": 1,
  "prompting_post_id": 97,
  "content": "Nam sit delectus debitis consectetur.",
  "state": "published",
  "published_on": "2007-01-10 15:39:45",
  "edited_on": null,
  "created_at": null,
  "updated_at": "2018-07-13 14:36:12",
  "deleted_at": null
}
```
``` 404 ``` when user does not have permission to update posts\
``` 404 ``` when specified post does not exist\
``` 422 ``` validation errors
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
    }
  ]
}
```

### Like Post - JSON controller

```
PUT /forums/post/like/{id}
```

#### Permission required `like-posts`

#### Request Example

```
var postId = 1;

$.ajax({
    url: '/forums/post/like/' + postId,
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

| path\|query\|body | key  | required | default | description\|notes           |
| ----------------- | ---- | -------- | ------- | ---------------------------- |
| path              | {id} | yes      |         | The post id to mark as liked |

#### Response Example

`200 OK`
```
{
  "id": 1,
  "post_id": 1,
  "liker_id": 1,
  "liked_on": "2018-07-06 07:15:02",
  "created_at": "2018-07-06 07:15:02",
  "updated_at": "2018-07-06 07:15:02"
}
```
```404``` when user does not have permission to like posts\
``` 404 ``` when specified post does not exist

### Unlike Post - JSON controller

```
DELETE /forums/post/unlike/{id}
```

#### Permission required `like-posts`

#### Request Example

```
var postId = 1;

$.ajax({
    url: '/forums/post/unlike/' + postId,
    type: 'delete',
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
| path              | {id} | yes      |         | The post id to mark as unliked |

#### Response Example

``` 204 ``` No content\
``` 404 ``` when user does not have permission to like posts\
``` 404 ``` when specified post does not exist

### Index Post - JSON controller

```
GET /forums/post/index
```

#### Permission required `index-posts`

#### Request Example

```
$.ajax({
    url: '/forums/post/index',
    type: 'put',
    data: {amount: 2, page: 1, thread_id: 1},
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

| path\|query\|body | key       | required | default | description\|notes                   |
| ----------------- | --------- | -------- | ------- | ------------------------------------ |
| query             | thread_id | yes      | null    | The thread id of the posts to return |
| query             | amount    | no       | 10      | The amount of posts to return        |
| query             | page      | no       | 1       | The page of posts to return          |

#### Response Example

` 200 OK `
```
{
  "posts": [
    {
      "id": 17,
      "thread_id": 1,
      "author_id": 1,
      "prompting_post_id": 1846,
      "content": "Nihil in iste quia ut voluptatem explicabo ex nihil asperiores minima nihil rerum iste cumque quia.",
      "state": "published",
      "published_on": "1970-09-08 16:35:18",
      "edited_on": null,
      "created_at": null,
      "updated_at": null,
      "deleted_at": null,
      "author_display_name": "justine14367813251",
      "like_count": 0,
      "is_liked_by_viewer": 0,
      "liker_1_id": null,
      "liker_1_display_name": null,
      "liker_2_id": null,
      "liker_2_display_name": null,
      "liker_3_id": null,
      "liker_3_display_name": null
    },
    {
      "id": 16,
      "thread_id": 1,
      "author_id": 1,
      "prompting_post_id": 8160594,
      "content": "Id ut optio et quae sed velit magni pariatur delectus et et ipsam amet molestias aut modi deleniti numquam et maiores et delectus harum aperiam et.",
      "state": "published",
      "published_on": "1973-04-08 14:04:16",
      "edited_on": null,
      "created_at": null,
      "updated_at": null,
      "deleted_at": null,
      "author_display_name": "justine14367813251",
      "like_count": 0,
      "is_liked_by_viewer": 0,
      "liker_1_id": null,
      "liker_1_display_name": null,
      "liker_2_id": null,
      "liker_2_display_name": null,
      "liker_3_id": null,
      "liker_3_display_name": null
    }
  ],
  "count": 20
}
```
``` 404 ``` when user does not have permission to index posts
```402``` validation error
```
{
  "status": "error",
  "code": 422,
  "total_results": 0,
  "results": [],
  "errors": [
    {
      "source": "thread_id",
      "detail": "The thread id field is required."
    }
  ]
}
```

### Show Post - JSON controller

```
GET /forums/post/show/{id}
```

#### Permission required `show-posts`

#### Request Example

```
var postId = 5;

$.ajax({
    url: '/forums/post/show/' + postId,
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

| path\|query\|body | key  | required | default | description\|notes  |
| ----------------- | ---- | -------- | ------- | ------------------- |
| path              | {id} | yes      |         | The post id to show |

#### Response Example

` 200 OK `
```
{
  "id": 5,
  "thread_id": 1,
  "author_id": 1,
  "prompting_post_id": 182692294,
  "content": "Magni maxime ut ea inventore maxime nemo enim esse eum magnam eaque aperiam qui beatae recusandae.",
  "state": "published",
  "published_on": "2002-05-16 21:48:44",
  "edited_on": null,
  "created_at": null,
  "updated_at": null,
  "deleted_at": null,
  "author_display_name": "berenice39811594521",
  "like_count": 5,
  "is_liked_by_viewer": 1,
  "liker_1_id": 1,
  "liker_1_display_name": "berenice39811594521",
  "liker_2_id": 3,
  "liker_2_display_name": "brown.delfina643461",
  "liker_3_id": 2,
  "liker_3_display_name": "sadye.smitham99",
  "reply_parents": [
    {
      "id": 1,
      "thread_id": 1,
      "author_id": 1,
      "prompting_post_id": 1179483,
      "content": "Ipsam est quis molestias officia dolorem est ipsam est suscipit laudantium tempore veritatis.",
      "state": "published",
      "published_on": "2014-05-10 08:11:44",
      "edited_on": null,
      "created_at": null,
      "updated_at": null,
      "deleted_at": null
    },
    {
      "id": 2,
      "thread_id": 1,
      "author_id": 1,
      "prompting_post_id": 342,
      "content": "Inventore enim nobis quaerat et et consequatur qui itaque est nihil culpa molestiae officia nesciunt labore tenetur beatae quis quod quae nam ut.",
      "state": "published",
      "published_on": "1973-12-29 09:04:43",
      "edited_on": null,
      "created_at": null,
      "updated_at": null,
      "deleted_at": null
    },
    {
      "id": 3,
      "thread_id": 1,
      "author_id": 1,
      "prompting_post_id": 57930,
      "content": "Quisquam aut laboriosam eaque quod adipisci est dolore suscipit expedita qui deserunt esse ut perspiciatis sapiente consectetur autem odit et aut sed nisi.",
      "state": "published",
      "published_on": "2003-08-23 14:24:59",
      "edited_on": null,
      "created_at": null,
      "updated_at": null,
      "deleted_at": null
    }
  ]
}
```
``` 404 ``` when user does not have permission to show posts\
``` 404 ``` when specified post does not exist

### Search Posts and/or Threads - JSON controller

```
GET /forums/search
```

#### Request Example

```
var term = 'similique quidem dolorum suscipit eligendi';

$.ajax({
    url: '/forums/search',
    type: 'put',
    data: {term: term, page: 1, limit: 3},
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

| path\|query\|body | key   | required | default | description\|notes                                   |
| ----------------- | ----- | -------- | ------- | ---------------------------------------------------- |
| query             | term  | no       | null    | The term(s) to search for                            |
| query             | type  | no       | null    | The type of results to return. 'posts' or 'threads'. |
| query             | page  | no       | 1       | The page of results to return                        |
| query             | limit | no       | 10      | The amount of results to return                      |
| query             | sort  | no       | score   | The column to sort results by                        |

#### Response Example

` 200 OK `
```
{
  "status": "ok",
  "code": 200,
  "page": 1,
  "limit": 3,
  "total_results": 8,
  "results": [
    {
      "id": 3,
      "category_id": 1,
      "author_id": 4,
      "title": "Autem qui quidem sit suscipit eligendi rerum quo et dolorum minima commodi similique.",
      "slug": "nemo-labore-aut-explicabo-tenetur",
      "pinned": 0,
      "locked": 0,
      "state": "published",
      "post_count": 3,
      "published_on": "1973-10-03 11:18:17",
      "created_at": null,
      "updated_at": null,
      "deleted_at": null,
      "last_post_published_on": "2011-04-17 01:46:40",
      "last_post_id": 4,
      "last_post_user_id": 4,
      "last_post_user_display_name": "daniel.elsie1",
      "is_read": 0,
      "is_followed": 0
    },
    {
      "id": 6,
      "thread_id": 3,
      "author_id": 4,
      "prompting_post_id": 24,
      "content": "Sint est quis debitis a similique dignissimos perspiciatis laboriosam at cum dolor quibusdam dignissimos alias dolorem corrupti aliquam quae.",
      "state": "published",
      "published_on": "2005-04-01 02:12:24",
      "edited_on": null,
      "created_at": null,
      "updated_at": null,
      "deleted_at": null,
      "author_display_name": "daniel.elsie1",
      "like_count": 0,
      "is_liked_by_viewer": 0,
      "liker_1_id": null,
      "liker_1_display_name": null,
      "liker_2_id": null,
      "liker_2_display_name": null,
      "liker_3_id": null,
      "liker_3_display_name": null
    },
    {
      "id": 11,
      "thread_id": 5,
      "author_id": 6,
      "prompting_post_id": 778272324,
      "content": "Et et sunt illum et aut reprehenderit animi nihil numquam nihil eum laborum magnam eligendi quaerat esse odio.",
      "state": "published",
      "published_on": "2015-08-17 18:58:39",
      "edited_on": null,
      "created_at": null,
      "updated_at": null,
      "deleted_at": null,
      "author_display_name": "brendon.cassin3661",
      "like_count": 0,
      "is_liked_by_viewer": 0,
      "liker_1_id": null,
      "liker_1_display_name": null,
      "liker_2_id": null,
      "liker_2_display_name": null,
      "liker_3_id": null,
      "liker_3_display_name": null
    }
  ],
  "filter_options": null
}
```

### Store Discussion - forms controller

```
PUT /discussion/store
```

#### Permission required `create-discussions`

#### Request Example

```
<form action="/discussion/store" method="POST">
  <input name="_method" type="hidden" value="PUT">

  <input name="title" type="text" required maxlength="255">
  <input name="description" type="text">
  <input name="topic" type="text">
</form>
```

#### Request Parameters

| path\|query\|body | key                | required | default            | description\|notes             |
| ----------------- | ------------------ | -------- | ------------------ | ------------------------------ |
| body              | _method            | yes      |                    | Set HTTP method verb to PUT    |
| body              | title              | yes      |                    | Discussion title                   |
| body              | description        | no       |                    | Discussion description             |


#### Response Example

``` 302 ```
Redirects to forums index url.\
Session has either success key set to true or error bag with validation errors.\
``` 403 ``` when user does not have permission to create discussion

### Update Discussion - forms controller

```
PATCH /discussion/update/{id}
```

#### Permission required `update-discussions`

#### Request Example

```
<form action="/discussion/update/{id}" method="POST">
  <input name="_method" type="hidden" value="PATCH">

  <input name="title" type="text" required maxlength="255">
</form>
```

#### Request Parameters

| path\|query\|body | key            | required | default            | description\|notes             |
| ----------------- | -------------- | -------- | ------------------ | ------------------------------ |
| path              | {id}           | yes      |                    | The discussion id to update        |
| body              | _method        | yes      |                    | Set HTTP method verb to PATCH  |
| body              | title          |  no      |                    | New discussion title               |
| body              | topic          |  no      |                    | New discussion topic               |
| body              | description    |  no      |                    | New discussion description               |

#### Response Example

``` 302 ```
Redirects to forum index URL. \
Session has either success key set to true or error bag with validation errors.\
``` 403 ``` when user does not have permission to update discussion\
``` 404 ``` when specified discussion does not exist


## Mobile endpoints

### Index Discussions(Topics) 

```
GET /forums/api/discussions/index
```

#### Permission required `index-discussions`

#### Request Example

```
$.ajax({
    url: '/forums/api/discussions/index',
    type: 'get',
    data: {amount: 2, page: 1},
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
| query             | amount       | no       | 10      | The amount of discussions to return              |
| query             | page         | no       | 1       | The page of discussions to return                |


#### Response Example

` 200 OK `
```json
{
    "status": "ok",
    "code": 200,
    "page": 1,
    "limit": 10,
    "total_results": 7,
    "results": [
        {
            "id": 1,
            "title": "General Piano Discussion",
            "slug": "general-piano-discussion",
            "description": "Repellendus qui facere et eaque voluptatem sint ad vel. Architecto quia a nemo tenetur aspernatur aliquid. Laborum consequuntur sed dolor et. Consectetur natus est enim maiores aspernatur. Vel ea aliquam ab laboriosam qui dolores quas.",
            "weight": 1,
            "created_at": "2021-05-05 07:11:18",
            "updated_at": null,
            "deleted_at": null,
            "brand": "pianote",
            "topic": null,
            "icon": "fa-piano",
            "mobile_app_url": "http://staging.pianote.com/forums/api/discussions/show/1",
            "post_count": 35874,
            "latest_post": {
                "id": 36624,
                "created_at": "2021-03-04 19:05:19",
                "thread_title": "Show Us Your Workspaces!",
                "author_id": 421390,
                "author_display_name": "Paul J.",
                "author_avatar_url": "https://d2vyvo0tyx8ig5.cloudfront.net/avatars/421390_1613116848632-1613116847-421390.jpg"
            }
        },
        {
            "id": 2,
            "title": "Pianote Member Discussion",
            "slug": "pianote-member-discussion",
            "description": "Nostrum dignissimos magnam ut animi. Quidem pariatur dolorem qui maiores aspernatur. Quo atque et culpa enim ducimus.",
            "weight": 2,
            "created_at": "2021-05-05 07:11:18",
            "updated_at": null,
            "deleted_at": null,
            "brand": "pianote",
            "topic": null,
            "icon": "fa-users",
            "mobile_app_url": "http://staging.pianote.com/forums/api/discussions/show/2",
            "post_count": 0
        },
        {
            "id": 3,
            "title": "Pianote Packs Discussion",
            "slug": "pianote-packs-discussion",
            "description": "Ea at molestias ut. Voluptates quo sapiente explicabo aliquid accusantium est omnis dolorem. Ducimus amet dolores atque nihil excepturi doloremque in. Voluptatem veritatis non qui ut iste quasi itaque.",
            "weight": 3,
            "created_at": "2021-05-05 07:11:18",
            "updated_at": null,
            "deleted_at": null,
            "brand": "pianote",
            "topic": null,
            "icon": "fa-cube",
            "mobile_app_url": "http://staging.pianote.com/forums/api/discussions/show/3",
            "post_count": 0
        },
        {
            "id": 4,
            "title": "Student Progress Discussion",
            "slug": "student-progress-discussion",
            "description": "Delectus aspernatur sint ratione non soluta occaecati earum nihil. Ut distinctio omnis velit officia ullam veniam repellendus ducimus. Autem cupiditate inventore sit harum ut. Aperiam et est ipsam id facere et.",
            "weight": 4,
            "created_at": "2021-05-05 07:11:18",
            "updated_at": null,
            "deleted_at": null,
            "brand": "pianote",
            "topic": null,
            "icon": "fa-signal-3",
            "mobile_app_url": "http://staging.pianote.com/forums/api/discussions/show/4",
            "post_count": 0
        },
        {
            "id": 5,
            "title": "Off-Topic Discussion",
            "slug": "offtopic-discussion",
            "description": "Fuga totam esse doloremque. Unde ducimus libero omnis sequi tempore voluptas ut repudiandae. Autem rem quos sequi consequatur.",
            "weight": 5,
            "created_at": "2021-05-05 07:11:18",
            "updated_at": null,
            "deleted_at": null,
            "brand": "pianote",
            "topic": null,
            "icon": "fa-comments",
            "mobile_app_url": "http://staging.pianote.com/forums/api/discussions/show/5",
            "post_count": 0
        },
        {
            "id": 6,
            "title": "Website Feedback & Update Log",
            "slug": "website-feedback-and-update-log",
            "description": "Et aliquam et praesentium reprehenderit. Cum blanditiis eligendi et adipisci ullam dolorum cumque. Dolorem doloribus dignissimos dicta. Illum qui a dolores.",
            "weight": 6,
            "created_at": "2021-05-05 07:11:18",
            "updated_at": null,
            "deleted_at": null,
            "brand": "pianote",
            "topic": null,
            "icon": "fa-browser",
            "mobile_app_url": "http://staging.pianote.com/forums/api/discussions/show/6",
            "post_count": 0
        },
        {
            "id": 7,
            "title": "Pianote Mod Forum",
            "slug": "pianote-mod-forum",
            "description": "Et dolores error sed molestias hic ex ex. Reiciendis aliquam est eum vel modi accusamus.",
            "weight": 7,
            "created_at": "2021-05-05 07:11:18",
            "updated_at": null,
            "deleted_at": null,
            "brand": "pianote",
            "topic": null,
            "icon": "fa-user-shield",
            "mobile_app_url": "http://staging.pianote.com/forums/api/discussions/show/7",
            "post_count": 0
        }
    ],
    "filter_options": null
}
```

``` 404 ``` when user does not have permission to index discussions