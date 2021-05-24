- [Install](#install)
- [Configure](#configure)
- [API Reference](#api-reference)
  * [Store Thread - forms controller](#store-thread---forms-controller)
    + [Permission required `create-threads`](#permission-required--create-threads-)
    + [Request Example](#request-example)
    + [Request Parameters](#request-parameters)
    + [Response Example](#response-example)
  * [Update Thread - forms controller](#update-thread---forms-controller)
    + [Permission required `update-threads`](#permission-required--update-threads-)
    + [Request Example](#request-example-1)
    + [Request Parameters](#request-parameters-1)
    + [Response Example](#response-example-1)
  * [Mark Thread as read - forms controller](#mark-thread-as-read---forms-controller)
    + [Permission required `read-threads`](#permission-required--read-threads-)
    + [Request Example](#request-example-2)
    + [Request Parameters](#request-parameters-2)
    + [Response Example](#response-example-2)
  * [Follow Thread - forms controller](#follow-thread---forms-controller)
    + [Permission required `follow-threads`](#permission-required--follow-threads-)
    + [Request Example](#request-example-3)
    + [Request Parameters](#request-parameters-3)
    + [Response Example](#response-example-3)
  * [Unfollow Thread - forms controller](#unfollow-thread---forms-controller)
    + [Permission required `follow-threads`](#permission-required--follow-threads--1)
    + [Request Example](#request-example-4)
    + [Request Parameters](#request-parameters-4)
    + [Response Example](#response-example-4)
  * [Delete Thread - form controller](#delete-thread---form-controller)
    + [Permission required `delete-threads`](#permission-required--delete-threads-)
    + [Request Example](#request-example-5)
    + [Request Parameters](#request-parameters-5)
    + [Response Example](#response-example-5)
  * [Store Thread - JSON controller](#store-thread---json-controller)
    + [Permission required `create-threads`](#permission-required--create-threads--1)
    + [Request Example](#request-example-6)
    + [Request Parameters](#request-parameters-6)
    + [Response Example](#response-example-6)
  * [Update Thread - JSON controller](#update-thread---json-controller)
    + [Permission required `update-threads`](#permission-required--update-threads--1)
    + [Request Example](#request-example-7)
    + [Request Parameters](#request-parameters-7)
    + [Response Example](#response-example-7)
  * [Mark Thread as read - JSON controller](#mark-thread-as-read---json-controller)
    + [Permission required `read-threads`](#permission-required--read-threads--1)
    + [Request Example](#request-example-8)
    + [Request Parameters](#request-parameters-8)
    + [Response Example](#response-example-8)
  * [Follow Thread - JSON controller](#follow-thread---json-controller)
    + [Permission required `follow-threads`](#permission-required--follow-threads--2)
    + [Request Example](#request-example-9)
    + [Request Parameters](#request-parameters-9)
    + [Response Example](#response-example-9)
  * [Unfollow Thread - JSON controller](#unfollow-thread---json-controller)
    + [Permission required `follow-threads`](#permission-required--follow-threads--3)
    + [Request Example](#request-example-10)
    + [Request Parameters](#request-parameters-10)
    + [Response Example](#response-example-10)
  * [Index Thread - JSON controller](#index-thread---json-controller)
    + [Permission required `index-threads`](#permission-required--index-threads-)
    + [Request Example](#request-example-11)
    + [Request Parameters](#request-parameters-11)
    + [Response Example](#response-example-11)
  * [Show Thread - JSON controller](#show-thread---json-controller)
    + [Permission required `show-threads`](#permission-required--show-threads-)
    + [Request Example](#request-example-12)
    + [Request Parameters](#request-parameters-12)
    + [Response Example](#response-example-12)
  * [Delete Thread - JSON controller](#delete-thread---json-controller)
    + [Permission required `delete-threads`](#permission-required--delete-threads--1)
    + [Request Example](#request-example-13)
    + [Request Parameters](#request-parameters-13)
    + [Response Example](#response-example-13)
  * [Store Post - forms controller](#store-post---forms-controller)
    + [Permission required `create-posts`](#permission-required--create-posts-)
    + [Request Example](#request-example-14)
    + [Request Parameters](#request-parameters-14)
    + [Response Example](#response-example-14)
  * [Update Post - forms controller](#update-post---forms-controller)
    + [Request Example](#request-example-15)
    + [Permission required `update-posts`](#permission-required--update-posts-)
    + [Request Parameters](#request-parameters-15)
    + [Response Example](#response-example-15)
  * [Like Post - forms controller](#like-post---forms-controller)
    + [Permission required `like-posts`](#permission-required--like-posts-)
    + [Request Example](#request-example-16)
    + [Request Parameters](#request-parameters-16)
    + [Response Example](#response-example-16)
  * [Unlike Post - forms controller](#unlike-post---forms-controller)
    + [Permission required `like-posts`](#permission-required--like-posts--1)
    + [Request Example](#request-example-17)
    + [Request Parameters](#request-parameters-17)
    + [Response Example](#response-example-17)
  * [Store Post - JSON controller](#store-post---json-controller)
    + [Permission required `create-posts`](#permission-required--create-posts--1)
    + [Request Example](#request-example-18)
    + [Request Parameters](#request-parameters-18)
    + [Response Example](#response-example-18)
  * [Update Post - JSON controller](#update-post---json-controller)
    + [Request Example](#request-example-19)
    + [Permission required `update-posts`](#permission-required--update-posts--1)
    + [Request Parameters](#request-parameters-19)
    + [Response Example](#response-example-19)
  * [Like Post - JSON controller](#like-post---json-controller)
    + [Permission required `like-posts`](#permission-required--like-posts--2)
    + [Request Example](#request-example-20)
    + [Request Parameters](#request-parameters-20)
    + [Response Example](#response-example-20)
  * [Unlike Post - JSON controller](#unlike-post---json-controller)
    + [Permission required `like-posts`](#permission-required--like-posts--3)
    + [Request Example](#request-example-21)
    + [Request Parameters](#request-parameters-21)
    + [Response Example](#response-example-21)
  * [Index Post - JSON controller](#index-post---json-controller)
    + [Permission required `index-posts`](#permission-required--index-posts-)
    + [Request Example](#request-example-22)
    + [Request Parameters](#request-parameters-22)
    + [Response Example](#response-example-22)
  * [Show Post - JSON controller](#show-post---json-controller)
    + [Permission required `show-posts`](#permission-required--show-posts-)
    + [Request Example](#request-example-23)
    + [Request Parameters](#request-parameters-23)
    + [Response Example](#response-example-23)
  * [Search Posts and/or Threads - JSON controller](#search-posts-and-or-threads---json-controller)
    + [Request Example](#request-example-24)
    + [Request Parameters](#request-parameters-24)
    + [Response Example](#response-example-24)
  * [Store Discussion - forms controller](#store-discussion---forms-controller)
    + [Permission required `create-discussions`](#permission-required--create-discussions-)
    + [Request Example](#request-example-25)
    + [Request Parameters](#request-parameters-25)
    + [Response Example](#response-example-25)
  * [Update Discussion - forms controller](#update-discussion---forms-controller)
    + [Permission required `update-discussions`](#permission-required--update-discussions-)
    + [Request Example](#request-example-26)
    + [Request Parameters](#request-parameters-26)
    + [Response Example](#response-example-26)
- [Mobile endpoints](#mobile-endpoints)
  * [Index Discussions(Topics)](#index-discussions-topics-)
    + [Permission required `index-discussions`](#permission-required--index-discussions-)
    + [Request Example](#request-example-27)
    + [Request Parameters](#request-parameters-27)
    + [Response Example](#response-example-27)
  * [Index Threads](#index-threads)
    + [Permission required `index-threads`](#permission-required--index-threads--1)
    + [Request Example](#request-example-28)
    + [Request Parameters](#request-parameters-28)
    + [Response Example](#response-example-28)
  * [Show Thread](#show-thread)
    + [Permission required `show-threads`](#permission-required--show-threads--1)
    + [Request Example](#request-example-29)
    + [Request Parameters](#request-parameters-29)
    + [Response Example](#response-example-29)
  * [Create Thread](#create-thread)
    + [Permission required `create-threads`](#permission-required--create-threads--2)
    + [Request Example](#request-example-30)
    + [Request Parameters](#request-parameters-30)
    + [Response Example](#response-example-30)
  * [Update Thread](#update-thread)
    + [Permission required `update-threads`](#permission-required--update-threads--2)
    + [Request Example](#request-example-31)
    + [Request Parameters](#request-parameters-31)
    + [Response Example](#response-example-31)
  * [Follow Thread](#follow-thread)
    + [Permission required `follow-threads`](#permission-required--follow-threads--4)
    + [Request Example](#request-example-32)
    + [Request Parameters](#request-parameters-32)
    + [Response Example](#response-example-32)
  * [Unfollow Thread](#unfollow-thread)
    + [Permission required `follow-threads`](#permission-required--follow-threads--5)
    + [Request Example](#request-example-33)
    + [Request Parameters](#request-parameters-33)
    + [Response Example](#response-example-33)
  * [Delete Thread - form controller](#delete-thread---form-controller-1)
    + [Permission required `delete-threads`](#permission-required--delete-threads--2)
    + [Request Example](#request-example-34)
    + [Request Parameters](#request-parameters-34)
    + [Response Example](#response-example-34)
  * [Create Post](#create-post)
    + [Permission required `create-posts`](#permission-required--create-posts--2)
    + [Request Example](#request-example-35)
    + [Request Parameters](#request-parameters-35)
    + [Response Example](#response-example-35)
  * [Update Post](#update-post)
    + [Request Example](#request-example-36)
    + [Permission required `update-posts`](#permission-required--update-posts--2)
    + [Request Parameters](#request-parameters-36)
    + [Response Example](#response-example-36)
  * [Report Post](#report-post)
    + [Permission required `report-posts`](#permission-required--report-posts-)
    + [Request Example](#request-example-37)
    + [Request Parameters](#request-parameters-37)
    + [Response Example](#response-example-37)
  * [Like Post](#like-post)
    + [Permission required `like-posts`](#permission-required--like-posts--4)
    + [Request Example](#request-example-38)
    + [Request Parameters](#request-parameters-38)
    + [Response Example](#response-example-38)
  * [Unlike Post - JSON controller](#unlike-post---json-controller-1)
    + [Permission required `like-posts`](#permission-required--like-posts--5)
    + [Request Example](#request-example-39)
    + [Request Parameters](#request-parameters-39)
    + [Response Example](#response-example-39)
  * [Search](#search)
    + [Request Example](#request-example-40)
    + [Request Parameters](#request-parameters-40)
    + [Response Example](#response-example-40)
  * [Forum Rules](#forum-rules)

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


### Index Threads

```
GET /forums/api/thread/index
```

#### Permission required `index-threads`

#### Request Example

```
$.ajax({
    url: '/forums/api/thread/index',
    type: 'get',
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
| query             | category_id  | no       | null    | The category id (topic) of threads to return |
| query             | pinned       | no       | null    | Boolean, the type of threads to return       |
| query             | followed     | no       | null    | Boolean, the type of threads to return       |

#### Response Example

` 200 OK `
```json
{
    "status": "ok",
    "code": 200,
    "page": 1,
    "limit": 10,
    "total_results": 3,
    "results": [
        {
            "id": 1886,
            "category_id": 1,
            "author_id": 412470,
            "title": "Show Us Your Workspaces!",
            "slug": "show-us-your-workspaces",
            "pinned": 0,
            "locked": 0,
            "state": "published",
            "post_count": 60,
            "last_post_id": 36625,
            "published_on": "2021-01-26 02:43:27",
            "created_at": "2021-01-26 02:43:27",
            "updated_at": "2021-01-26 02:43:27",
            "deleted_at": null,
            "version_master_id": null,
            "version_saved_at": null,
            "category_slug": "general-piano-discussion",
            "category": "General Piano Discussion",
            "last_post_published_on": "2021-03-04 19:04:10",
            "last_post_user_id": 405877,
            "is_read": 1,
            "is_followed": 1,
            "mobile_app_url": "http://staging.pianote.com/forums/api/thread/show/1886",
            "author_display_name": "Mark Nicholson",
            "author_avatar_url": "https://d2vyvo0tyx8ig5.cloudfront.net/avatars/412470_1610337691197-1610337692-412470.jpg",
            "author_access_level": "piano"
        },
        {
            "id": 2017,
            "category_id": 1,
            "author_id": 424755,
            "title": "Help dont know where to start!",
            "slug": "help-dont-know-where-to-start",
            "pinned": 0,
            "locked": 0,
            "state": "published",
            "post_count": 11,
            "last_post_id": 36622,
            "published_on": "2021-02-27 15:17:19",
            "created_at": "2021-02-27 15:17:19",
            "updated_at": "2021-02-27 15:17:19",
            "deleted_at": null,
            "version_master_id": null,
            "version_saved_at": null,
            "category_slug": "general-piano-discussion",
            "category": "General Piano Discussion",
            "last_post_published_on": "2021-03-04 18:45:21",
            "last_post_user_id": 421390,
            "is_read": 1,
            "is_followed": 1,
            "mobile_app_url": "http://staging.pianote.com/forums/api/thread/show/2017",
            "author_display_name": "lesleycatnursey",
            "author_avatar_url": "https://s3.amazonaws.com/pianote/defaults/avatar.png",
            "author_access_level": "piano"
        },
        {
            "id": 2030,
            "category_id": 1,
            "author_id": 417888,
            "title": "Foundations books",
            "slug": "foundations-books",
            "pinned": 0,
            "locked": 0,
            "state": "published",
            "post_count": 3,
            "last_post_id": 36619,
            "published_on": "2021-03-04 09:18:27",
            "created_at": "2021-03-04 09:18:27",
            "updated_at": "2021-03-04 09:18:27",
            "deleted_at": null,
            "version_master_id": null,
            "version_saved_at": null,
            "category_slug": "general-piano-discussion",
            "category": "General Piano Discussion",
            "last_post_published_on": "2021-03-04 17:36:27",
            "last_post_user_id": 417888,
            "is_read": 1,
            "is_followed": 1,
            "mobile_app_url": "http://staging.pianote.com/forums/api/thread/show/2030",
            "author_display_name": "Wayne Pevy",
            "author_avatar_url": "https://d2vyvo0tyx8ig5.cloudfront.net/avatars/avatar-1614881481-417888.jpg",
            "author_access_level": "piano"
        }
    ],
    "filter_options": null
}
```
``` 404 ``` when user does not have permission to index threads

### Show Thread

```
GET /forums/api/thread/show/{id}
```

#### Permission required `show-threads`

#### Request Example

```
var threadId = 1;

$.ajax({
    url: '/forums/api/thread/show/' + threadId,
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
| query             | amount       | no       | 10      | The amount of posts to return              |
| query             | page         | no       | 1       | The page of posts to return                |

#### Response Example

` 200 OK `
```json
{
  "id": 5375,
  "category_id": 6,
  "author_id": 266419,
  "title": "Team links 404",
  "slug": "team-links-404",
  "pinned": 0,
  "locked": 0,
  "state": "published",
  "post_count": 13,
  "published_on": "2014-12-28 00:51:52",
  "created_at": "2014-12-28 00:51:52",
  "updated_at": "2015-01-27 21:44:37",
  "deleted_at": null,
  "category_slug": "drumeo-website-feedback",
  "category": "Drumeo Website Feedback",
  "is_read": 0,
  "is_followed": 0,
  "mobile_app_url": "https://dev.drumeo.com/laravel/public/forums/api/thread/show/5375",
  "author_display_name": "AndyOBrien",
  "author_avatar_url": "https://drumeo-user-avatars.s3-us-west-2.amazonaws.com/100055_avatar_url_1476034853.jpg",
  "author_access_level": "pack",
  "published_on_formatted": "Dec 28, 2014",
  "latest_post": {
    "id": 124146,
    "created_at": "2015-01-27 21:44:37",
    "created_at_diff": "6 years ago",
    "author_id": 149641,
    "author_display_name": "Chad",
    "author_avatar_url": "https://s3.amazonaws.com/pianote/defaults/avatar.png"
  },
  "posts": [
    {
      "id": 121659,
      "thread_id": 5375,
      "author_id": 266419,
      "prompting_post_id": null,
      "content": "<p>.</p><br />",
      "state": "published",
      "published_on": "2014-12-28 00:51:52",
      "edited_on": "2015-10-07 18:52:09",
      "created_at": "2014-12-28 00:51:52",
      "updated_at": "2015-10-07 18:52:09",
      "deleted_at": null,
      "like_count": 0,
      "is_liked_by_viewer": 0,
      "url": "https://dev.drumeo.com/laravel/public/post/update/121659",
      "author_display_name": "AndyOBrien",
      "author_avatar_url": "https://drumeo-user-avatars.s3-us-west-2.amazonaws.com/100055_avatar_url_1476034853.jpg",
      "author_total_posts": 206,
      "author_days_as_member": 2400,
      "author_signature": null,
      "author_access_level": "pack",
      "author_xp": "0",
      "author_xp_rank": "Enthusiast I"
    },
    {
      "id": 121669,
      "thread_id": 5375,
      "author_id": 254567,
      "prompting_post_id": null,
      "content": "<p>Andy the one's I clicked on worked.</p><br />",
      "state": "published",
      "published_on": "2014-12-28 03:48:35",
      "edited_on": null,
      "created_at": "2014-12-28 03:48:35",
      "updated_at": null,
      "deleted_at": null,
      "like_count": 1,
      "is_liked_by_viewer": 0,
      "url": "https://dev.drumeo.com/laravel/public/post/update/121669",
      "author_display_name": "JackO",
      "author_avatar_url": "https://drumeo-user-avatars.s3-us-west-2.amazonaws.com/88101_avatar_url_1464799005.jpg",
      "author_total_posts": 316,
      "author_days_as_member": 2621,
      "author_signature": null,
      "author_access_level": "lifetime",
      "author_xp": 0,
      "author_xp_rank": "Enthusiast I"
    },
    {
      "id": 121674,
      "thread_id": 5375,
      "author_id": 266419,
      "prompting_post_id": null,
      "content": "<blockquote><span class=\"post-id\">121669</span><p class=\"quote-heading\"><strong>JackO</strong><em> - Dec 28, 2014</em></p><br /><p>Andy the one's I clicked on worked.</p><br /></blockquote><br />",
      "state": "published",
      "published_on": "2014-12-28 04:49:03",
      "edited_on": "2015-10-07 18:52:14",
      "created_at": "2014-12-28 04:49:03",
      "updated_at": "2015-10-07 18:52:14",
      "deleted_at": null,
      "like_count": 0,
      "is_liked_by_viewer": 0,
      "url": "https://dev.drumeo.com/laravel/public/post/update/121674",
      "author_display_name": "AndyOBrien",
      "author_avatar_url": "https://drumeo-user-avatars.s3-us-west-2.amazonaws.com/100055_avatar_url_1476034853.jpg",
      "author_total_posts": 206,
      "author_days_as_member": 2400,
      "author_signature": null,
      "author_access_level": "pack",
      "author_xp": "0",
      "author_xp_rank": "Enthusiast I"
    },
    {
      "id": 121688,
      "thread_id": 5375,
      "author_id": 167146,
      "prompting_post_id": null,
      "content": "<blockquote><span class=\"post-id\">121674</span><p class=\"quote-heading\"><strong>Andy OBrien</strong><em> - Dec 28, 2014</em></p><br /><blockquote><span class=\"post-id\">121669</span><p class=\"quote-heading\"><strong>JackO</strong><em> - Dec 28, 2014</em></p><br /><p>Andy the one's I clicked on worked.</p><br /></blockquote><br /></blockquote><br /><p>No.</p>",
      "state": "published",
      "published_on": "2014-12-28 14:16:51",
      "edited_on": null,
      "created_at": "2014-12-28 14:16:51",
      "updated_at": null,
      "deleted_at": null,
      "like_count": 2,
      "is_liked_by_viewer": 0,
      "url": "https://dev.drumeo.com/laravel/public/post/update/121688",
      "author_display_name": "Poco Askew",
      "author_avatar_url": "https://dzryyo1we6bm3.cloudfront.net/avatars/167146_1608939162460-1608939164-167146.jpg",
      "author_total_posts": 7666,
      "author_days_as_member": 3681,
      "author_signature": null,
      "author_access_level": "lifetime",
      "author_xp": "0",
      "author_xp_rank": "Enthusiast I"
    },
    {
      "id": 121699,
      "thread_id": 5375,
      "author_id": 254567,
      "prompting_post_id": null,
      "content": "<p>No.</p><br />",
      "state": "published",
      "published_on": "2014-12-28 15:39:07",
      "edited_on": null,
      "created_at": "2014-12-28 15:39:07",
      "updated_at": null,
      "deleted_at": null,
      "like_count": 1,
      "is_liked_by_viewer": 0,
      "url": "https://dev.drumeo.com/laravel/public/post/update/121699",
      "author_display_name": "JackO",
      "author_avatar_url": "https://drumeo-user-avatars.s3-us-west-2.amazonaws.com/88101_avatar_url_1464799005.jpg",
      "author_total_posts": 316,
      "author_days_as_member": 2621,
      "author_signature": null,
      "author_access_level": "lifetime",
      "author_xp": 0,
      "author_xp_rank": "Enthusiast I"
    },
    {
      "id": 122907,
      "thread_id": 5375,
      "author_id": 150473,
      "prompting_post_id": null,
      "content": "<blockquote><span class=\"post-id\">121659</span><p class=\"quote-heading\"><strong>Andy OBrien</strong><em> - Dec 28, 2014</em></p><br /><p>.</p><br /></blockquote><br /><p>Hey Andy,<br><br><br />These are actually older pages which arent updated/maintained anymore. Our up-to-date team page can be viewed at <a data-ipb='nomediaparse' href='http://drumeo.com/team/'>http://drumeo.com/team/</a></p>",
      "state": "published",
      "published_on": "2015-01-15 16:18:40",
      "edited_on": null,
      "created_at": "2015-01-15 16:18:40",
      "updated_at": null,
      "deleted_at": null,
      "like_count": 0,
      "is_liked_by_viewer": 0,
      "url": "https://dev.drumeo.com/laravel/public/post/update/122907",
      "author_display_name": "Trent",
      "author_avatar_url": "https://dzryyo1we6bm3.cloudfront.net/avatars/6280_1557856721503.jpg",
      "author_total_posts": 33,
      "author_days_as_member": 3396,
      "author_signature": null,
      "author_access_level": "admin",
      "author_xp": 0,
      "author_xp_rank": "Enthusiast I"
    },
    {
      "id": 122908,
      "thread_id": 5375,
      "author_id": 266419,
      "prompting_post_id": null,
      "content": "<blockquote><span class=\"post-id\">122907</span><p class=\"quote-heading\"><strong>Trent</strong><em> - Jan 15, 2015</em></p><br /><blockquote><span class=\"post-id\">121659</span><p class=\"quote-heading\"><strong>Andy OBrien</strong><em> - Dec 28, 2014</em></p><br /><p>.</p><br /></blockquote><br /><p>Hey Andy,<br><br><br />These are actually older pages which arent updated/maintained anymore. Our up-to-date team page can be viewed at <a data-ipb='nomediaparse' href='http://drumeo.com/team/'>http://drumeo.com/team/</a></p></blockquote><br />",
      "state": "published",
      "published_on": "2015-01-15 16:27:21",
      "edited_on": "2015-10-07 18:52:22",
      "created_at": "2015-01-15 16:27:21",
      "updated_at": "2015-10-07 18:52:22",
      "deleted_at": null,
      "like_count": 0,
      "is_liked_by_viewer": 0,
      "url": "https://dev.drumeo.com/laravel/public/post/update/122908",
      "author_display_name": "AndyOBrien",
      "author_avatar_url": "https://drumeo-user-avatars.s3-us-west-2.amazonaws.com/100055_avatar_url_1476034853.jpg",
      "author_total_posts": 206,
      "author_days_as_member": 2400,
      "author_signature": null,
      "author_access_level": "pack",
      "author_xp": "0",
      "author_xp_rank": "Enthusiast I"
    },
    {
      "id": 122983,
      "thread_id": 5375,
      "author_id": 8,
      "prompting_post_id": null,
      "content": "<blockquote><span class=\"post-id\">122908</span><p class=\"quote-heading\"><strong>Andy OBrien</strong><em> - Jan 15, 2015</em></p><br /><blockquote><span class=\"post-id\">122907</span><p class=\"quote-heading\"><strong>Trent</strong><em> - Jan 15, 2015</em></p><br /><blockquote><span class=\"post-id\">121659</span><p class=\"quote-heading\"><strong>Andy OBrien</strong><em> - Dec 28, 2014</em></p><br /><p>.</p><br /></blockquote><br /><p>Hey Andy,<br><br><br />These are actually older pages which arent updated/maintained anymore. Our up-to-date team page can be viewed at <a data-ipb='nomediaparse' href='http://drumeo.com/team/'>http://drumeo.com/team/</a></p></blockquote><br /></blockquote><br /><p>Hey Andy, I see where you are coming from, but it's not the case.  The instructor page is still valid, we are just doing a massive update to it, and have a temporary site active so we can test the new design out (common practice). Jai Es has changed his website URL, and since then his link does not work. Thanks for bringing this to our attention by the way! Sometimes URL's change, and with a massive site like Drumeo, unless the instructor informs us of this, it can be forgotten about.  I believe his new website is - <a data-ipb='nomediaparse' href='http://www.drumlife.me/'>http://www.drumlife.me/</a>in case you wanted to check it out. But the new \"team\" page is on it's way, and will replace the old one. Shouldn't be much longer.  </p><br /><p> </p><br /><p>As for our older products, we have discontinued many (Rock Drumming System, Jazz Drumming Secrets, Original Bass Drum Secrets, One Handed Roll...) as these were legitimately outdated, and our new packs are far superior in many ways. So some members will have these old packs still in their accounts, but they are not purchasable anymore. That is why you can find these lessons in the search bar, as this feature searches the entire site. </p><br /><p> </p><br /><p>We are a small team here at Drumeo, and do our best to keep the site updated and working properly. So please be patient with us, as we also have to prioritize our updates, which I hope you can understand. We have been blessed with an incredible community here that gives us amazing feedback to make the site better, and we appreciate the suggestions from all members. So if you find any other broken links, missing sheet music, or other issues with the site, please bring them to our attention and we will get them fixed as fast as we can :). </p>",
      "state": "published",
      "published_on": "2015-01-16 23:24:09",
      "edited_on": null,
      "created_at": "2015-01-16 23:24:09",
      "updated_at": null,
      "deleted_at": null,
      "like_count": 6,
      "is_liked_by_viewer": 0,
      "url": "https://dev.drumeo.com/laravel/public/post/update/122983",
      "author_display_name": "Dave Atkinson",
      "author_avatar_url": "https://dzryyo1we6bm3.cloudfront.net/avatars/8_1580854198342.jpg",
      "author_total_posts": 2749,
      "author_days_as_member": 3691,
      "author_signature": null,
      "author_access_level": "admin",
      "author_xp": "0",
      "author_xp_rank": "Enthusiast I"
    },
    {
      "id": 122985,
      "thread_id": 5375,
      "author_id": 150473,
      "prompting_post_id": null,
      "content": "<blockquote><span class=\"post-id\">122908</span><p class=\"quote-heading\"><strong>Andy OBrien</strong><em> - Jan 15, 2015</em></p><br /><blockquote><span class=\"post-id\">122907</span><p class=\"quote-heading\"><strong>Trent</strong><em> - Jan 15, 2015</em></p><br /><blockquote><span class=\"post-id\">121659</span><p class=\"quote-heading\"><strong>Andy OBrien</strong><em> - Dec 28, 2014</em></p><br /><p>.</p><br /></blockquote><br /><p>Hey Andy,<br><br><br />These are actually older pages which arent updated/maintained anymore. Our up-to-date team page can be viewed at <a data-ipb='nomediaparse' href='http://drumeo.com/team/'>http://drumeo.com/team/</a></p></blockquote><br /></blockquote><br /><p><br />That came out wrong, the pages are still accurate however like Dave said Jais website moved. I have launched a new version of the team page which can be viewed at <a  class=\"bbc_url\" href=\"http://drumeo.com/members/team/\">http://drumeo.com/members/team/</a>. All of the biography's are updated and all of the links are definitely working.<br><br><br />I would really appreciate your feedback on the new design!<br><br><br />Thanks</p>",
      "state": "published",
      "published_on": "2015-01-16 23:38:12",
      "edited_on": null,
      "created_at": "2015-01-16 23:38:12",
      "updated_at": null,
      "deleted_at": null,
      "like_count": 1,
      "is_liked_by_viewer": 0,
      "url": "https://dev.drumeo.com/laravel/public/post/update/122985",
      "author_display_name": "Trent",
      "author_avatar_url": "https://dzryyo1we6bm3.cloudfront.net/avatars/6280_1557856721503.jpg",
      "author_total_posts": 33,
      "author_days_as_member": 3396,
      "author_signature": null,
      "author_access_level": "admin",
      "author_xp": 0,
      "author_xp_rank": "Enthusiast I"
    },
    {
      "id": 122991,
      "thread_id": 5375,
      "author_id": 266419,
      "prompting_post_id": null,
      "content": "<blockquote><span class=\"post-id\">122983</span><p class=\"quote-heading\"><strong>Dave Atkinson</strong><em> - Jan 16, 2015</em></p><br /><blockquote><span class=\"post-id\">122908</span><p class=\"quote-heading\"><strong>Andy OBrien</strong><em> - Jan 15, 2015</em></p><br /><blockquote><span class=\"post-id\">122907</span><p class=\"quote-heading\"><strong>Trent</strong><em> - Jan 15, 2015</em></p><br /><blockquote><span class=\"post-id\">121659</span><p class=\"quote-heading\"><strong>Andy OBrien</strong><em> - Dec 28, 2014</em></p><br /><p>.</p><br /></blockquote><br /><p>Hey Andy,<br><br><br />These are actually older pages which arent updated/maintained anymore. Our up-to-date team page can be viewed at <a data-ipb='nomediaparse' href='http://drumeo.com/team/'>http://drumeo.com/team/</a></p></blockquote><br /></blockquote><br /><p>Hey Andy, I see where you are coming from, but it's not the case.  The instructor page is still valid, we are just doing a massive update to it, and have a temporary site active so we can test the new design out (common practice). Jai Es has changed his website URL, and since then his link does not work. Thanks for bringing this to our attention by the way! Sometimes URL's change, and with a massive site like Drumeo, unless the instructor informs us of this, it can be forgotten about.  I believe his new website is - <a data-ipb='nomediaparse' href='http://www.drumlife.me/'>http://www.drumlife.me/</a>in case you wanted to check it out. But the new \"team\" page is on it's way, and will replace the old one. Shouldn't be much longer.  </p><br /><p> </p><br /><p>As for our older products, we have discontinued many (Rock Drumming System, Jazz Drumming Secrets, Original Bass Drum Secrets, One Handed Roll...) as these were legitimately outdated, and our new packs are far superior in many ways. So some members will have these old packs still in their accounts, but they are not purchasable anymore. That is why you can find these lessons in the search bar, as this feature searches the entire site. </p><br /><p> </p><br /><p>We are a small team here at Drumeo, and do our best to keep the site updated and working properly. So please be patient with us, as we also have to prioritize our updates, which I hope you can understand. We have been blessed with an incredible community here that gives us amazing feedback to make the site better, and we appreciate the suggestions from all members. So if you find any other broken links, missing sheet music, or other issues with the site, please bring them to our attention and we will get them fixed as fast as we can :). </p></blockquote><br />",
      "state": "published",
      "published_on": "2015-01-17 00:45:06",
      "edited_on": "2015-10-07 18:52:49",
      "created_at": "2015-01-17 00:45:06",
      "updated_at": "2015-10-07 18:52:49",
      "deleted_at": null,
      "like_count": 2,
      "is_liked_by_viewer": 0,
      "url": "https://dev.drumeo.com/laravel/public/post/update/122991",
      "author_display_name": "AndyOBrien",
      "author_avatar_url": "https://drumeo-user-avatars.s3-us-west-2.amazonaws.com/100055_avatar_url_1476034853.jpg",
      "author_total_posts": 206,
      "author_days_as_member": 2400,
      "author_signature": null,
      "author_access_level": "pack",
      "author_xp": "0",
      "author_xp_rank": "Enthusiast I"
    },
    {
      "id": 122992,
      "thread_id": 5375,
      "author_id": 266419,
      "prompting_post_id": null,
      "content": "<blockquote><span class=\"post-id\">122985</span><p class=\"quote-heading\"><strong>Trent</strong><em> - Jan 16, 2015</em></p><br /><blockquote><span class=\"post-id\">122908</span><p class=\"quote-heading\"><strong>Andy OBrien</strong><em> - Jan 15, 2015</em></p><br /><blockquote><span class=\"post-id\">122907</span><p class=\"quote-heading\"><strong>Trent</strong><em> - Jan 15, 2015</em></p><br /><blockquote><span class=\"post-id\">121659</span><p class=\"quote-heading\"><strong>Andy OBrien</strong><em> - Dec 28, 2014</em></p><br /><p>.</p><br /></blockquote><br /><p>Hey Andy,<br><br><br />These are actually older pages which arent updated/maintained anymore. Our up-to-date team page can be viewed at <a data-ipb='nomediaparse' href='http://drumeo.com/team/'>http://drumeo.com/team/</a></p></blockquote><br /></blockquote><br /><p><br />That came out wrong, the pages are still accurate however like Dave said Jais website moved. I have launched a new version of the team page which can be viewed at <a  class=\"bbc_url\" href=\"http://drumeo.com/members/team/\">http://drumeo.com/members/team/</a>. All of the biography's are updated and all of the links are definitely working.<br><br><br />I would really appreciate your feedback on the new design!<br><br><br />Thanks</p></blockquote><br />",
      "state": "published",
      "published_on": "2015-01-17 00:50:57",
      "edited_on": "2015-10-07 18:52:54",
      "created_at": "2015-01-17 00:50:57",
      "updated_at": "2015-10-07 18:52:54",
      "deleted_at": null,
      "like_count": 1,
      "is_liked_by_viewer": 0,
      "url": "https://dev.drumeo.com/laravel/public/post/update/122992",
      "author_display_name": "AndyOBrien",
      "author_avatar_url": "https://drumeo-user-avatars.s3-us-west-2.amazonaws.com/100055_avatar_url_1476034853.jpg",
      "author_total_posts": 206,
      "author_days_as_member": 2400,
      "author_signature": null,
      "author_access_level": "pack",
      "author_xp": "0",
      "author_xp_rank": "Enthusiast I"
    },
    {
      "id": 123207,
      "thread_id": 5375,
      "author_id": 8,
      "prompting_post_id": null,
      "content": "<blockquote><span class=\"post-id\">122991</span><p class=\"quote-heading\"><strong>Andy OBrien</strong><em> - Jan 17, 2015</em></p><br /><blockquote><span class=\"post-id\">122983</span><p class=\"quote-heading\"><strong>Dave Atkinson</strong><em> - Jan 16, 2015</em></p><br /><blockquote><span class=\"post-id\">122908</span><p class=\"quote-heading\"><strong>Andy OBrien</strong><em> - Jan 15, 2015</em></p><br /><blockquote><span class=\"post-id\">122907</span><p class=\"quote-heading\"><strong>Trent</strong><em> - Jan 15, 2015</em></p><br /><blockquote><span class=\"post-id\">121659</span><p class=\"quote-heading\"><strong>Andy OBrien</strong><em> - Dec 28, 2014</em></p><br /><p>.</p><br /></blockquote><br /><p>Hey Andy,<br><br><br />These are actually older pages which arent updated/maintained anymore. Our up-to-date team page can be viewed at <a data-ipb='nomediaparse' href='http://drumeo.com/team/'>http://drumeo.com/team/</a></p></blockquote><br /></blockquote><br /><p>Hey Andy, I see where you are coming from, but it's not the case.  The instructor page is still valid, we are just doing a massive update to it, and have a temporary site active so we can test the new design out (common practice). Jai Es has changed his website URL, and since then his link does not work. Thanks for bringing this to our attention by the way! Sometimes URL's change, and with a massive site like Drumeo, unless the instructor informs us of this, it can be forgotten about.  I believe his new website is - <a data-ipb='nomediaparse' href='http://www.drumlife.me/'>http://www.drumlife.me/</a>in case you wanted to check it out. But the new \"team\" page is on it's way, and will replace the old one. Shouldn't be much longer.  </p><br /><p> </p><br /><p>As for our older products, we have discontinued many (Rock Drumming System, Jazz Drumming Secrets, Original Bass Drum Secrets, One Handed Roll...) as these were legitimately outdated, and our new packs are far superior in many ways. So some members will have these old packs still in their accounts, but they are not purchasable anymore. That is why you can find these lessons in the search bar, as this feature searches the entire site. </p><br /><p> </p><br /><p>We are a small team here at Drumeo, and do our best to keep the site updated and working properly. So please be patient with us, as we also have to prioritize our updates, which I hope you can understand. We have been blessed with an incredible community here that gives us amazing feedback to make the site better, and we appreciate the suggestions from all members. So if you find any other broken links, missing sheet music, or other issues with the site, please bring them to our attention and we will get them fixed as fast as we can :). </p></blockquote><br /></blockquote><br /><p>All good Andy, the feedback was legitimate, so we do appreciate it.  And I know we have discussed our old products quite a bit in the past, and our new ones cover those old products, expand on their concepts, and do it in a much more modern way (on screen graphics, less \"scripted\", etc...).  If there is one product that you would really like to get your hands on (digitally of course), email me directly and I can set you up depending on if we have the course online. But we do not want to advertise and offer our older packs publicly, as they just do not do Drumeo justice anymore ;).</p><br /><p> </p><br /><p> </p><br /><blockquote><span class=\"post-id\">122992</span><p class=\"quote-heading\"><strong>Andy OBrien</strong><em> - Jan 17, 2015</em></p><br /><blockquote><span class=\"post-id\">122985</span><p class=\"quote-heading\"><strong>Trent</strong><em> - Jan 16, 2015</em></p><br /><blockquote><span class=\"post-id\">122908</span><p class=\"quote-heading\"><strong>Andy OBrien</strong><em> - Jan 15, 2015</em></p><br /><blockquote><span class=\"post-id\">122907</span><p class=\"quote-heading\"><strong>Trent</strong><em> - Jan 15, 2015</em></p><br /><blockquote><span class=\"post-id\">121659</span><p class=\"quote-heading\"><strong>Andy OBrien</strong><em> - Dec 28, 2014</em></p><br /><p>.</p><br /></blockquote><br /><p>Hey Andy,<br><br><br />These are actually older pages which arent updated/maintained anymore. Our up-to-date team page can be viewed at <a data-ipb='nomediaparse' href='http://drumeo.com/team/'>http://drumeo.com/team/</a></p></blockquote><br /></blockquote><br /><p><br />That came out wrong, the pages are still accurate however like Dave said Jais website moved. I have launched a new version of the team page which can be viewed at <a  class=\"bbc_url\" href=\"http://drumeo.com/members/team/\">http://drumeo.com/members/team/</a>. All of the biography's are updated and all of the links are definitely working.<br><br><br />I would really appreciate your feedback on the new design!<br><br><br />Thanks</p></blockquote><br /></blockquote><br /><p>I really like this idea (and not because I would like more attention...). Not sure if this is possible with the new design, as the instructors drop downs now all appear on the one site, but I am sure Trent could look into it.</p>",
      "state": "published",
      "published_on": "2015-01-18 18:27:25",
      "edited_on": null,
      "created_at": "2015-01-18 18:27:25",
      "updated_at": null,
      "deleted_at": null,
      "like_count": 0,
      "is_liked_by_viewer": 0,
      "url": "https://dev.drumeo.com/laravel/public/post/update/123207",
      "author_display_name": "Dave Atkinson",
      "author_avatar_url": "https://dzryyo1we6bm3.cloudfront.net/avatars/8_1580854198342.jpg",
      "author_total_posts": 2749,
      "author_days_as_member": 3691,
      "author_signature": null,
      "author_access_level": "admin",
      "author_xp": "0",
      "author_xp_rank": "Enthusiast I"
    },
    {
      "id": 124146,
      "thread_id": 5375,
      "author_id": 149641,
      "prompting_post_id": null,
      "content": "<p>Oh come on Dave. You're already on the top row!</p><br />",
      "state": "published",
      "published_on": "2015-01-27 21:44:37",
      "edited_on": null,
      "created_at": "2015-01-27 21:44:37",
      "updated_at": null,
      "deleted_at": null,
      "like_count": 1,
      "is_liked_by_viewer": 0,
      "url": "https://dev.drumeo.com/laravel/public/post/update/124146",
      "author_display_name": "Chad",
      "author_avatar_url": "https://s3.amazonaws.com/pianote/defaults/avatar.png",
      "author_total_posts": 12,
      "author_days_as_member": 3337,
      "author_signature": null,
      "author_access_level": "admin",
      "author_xp": 0,
      "author_xp_rank": "Enthusiast I"
    }
  ]
}
```
``` 404 ``` when user does not have permission to show thread
``` 404 ``` when specified thread does not exist

### Create Thread

```
PUT /forums/api/thread/store
```

#### Permission required `create-threads`

#### Request Example

```
$.ajax({
    url: '/forums/api/thread/store',
    type: 'put'
  	data: {title: 'Thread title', first_post_content: 'Lorem ipsum ...', category_id:1} 
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

| path\|query\|body | key                | required | default            | description\|notes             |
| ----------------- | ------------------ | -------- | ------------------ | ------------------------------ |
| body              | title              | yes      |                    | Thread title                   |
| body              | first_post_content | yes      |                    | First post content             |
| body              | category_id        | yes      |         | The category (topic) id this thread belongs to |

#### Response Example

`200 OK`
```json
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
``` 404 ``` when user does not have permission to create threads

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

### Update Thread
```
PATCH /forums/api/thread/update/{id}
```

#### Permission required `update-threads`

#### Request Example

```
var title = 'Consectetur officia hic possimus iure et minima minima ut.';
var threadId = 1;

$.ajax({
    url: '/forums/api/thread/update/' + threadId,
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
```404``` when user does not have permission to update the thread
```404``` when the specified thread id is not found
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
### Follow Thread
```
PUT /forums/api/thread/follow/{id}
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
``` 404 ``` when user does not have permission to follow threads
``` 404 ``` when specified thread does not exist


### Unfollow Thread

```
DELETE /forums/api/thread/unfollow/{id}
```

#### Permission required `follow-threads`

#### Request Example

```
var threadId = 1;

$.ajax({
    url: '/forums/api/thread/unfollow/' + threadId,
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

| path\|query\|body | key  | required | default | description\|notes        |
| ----------------- | ---- | -------- | ------- | ------------------------- |
| path              | {id} | yes      |         | The thread id to unfollow |

#### Response Example

``` 204 ``` No content\
``` 404 ``` when user does not have permission to follow threads\
``` 404 ``` when specified thread does not exist

### Delete Thread - form controller

```
DELETE /forums/api/thread/delete/{id}
```

#### Permission required `delete-threads`

#### Request Example

```
var threadId = 1;

$.ajax({
url: '/forums/api/thread/delete/' + threadId,
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

| path\|query\|body | key  | required | default | description\|notes        |
| ----------------- | ---- | -------- | ------- | ------------------------- |
| path              | {id} | yes      |         | The thread id to delete |

#### Response Example

``` 204 ``` No content\
``` 404 ``` when user does not have permission to delete threads\
``` 404 ``` when specified thread does not exist


### Create Post 

```
PUT /forums/api/post/store
```

#### Permission required `create-posts`

#### Request Example

```
var content = 'Temporibus provident modi quo.';
var threadId = 1;

$.ajax({
    url: '/forums/api/post/store',
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
| body              | parent_ids        | no       |         |Array with parent ids for reply post|

#### Response Example

`200 OK`
```
{
    "id": 36634,
    "thread_id": 4,
    "author_id": 149628,
    "prompting_post_id": null,
    "content": "Reply pt 36631",
    "state": "published",
    "published_on": "2021-05-24 10:11:39",
    "edited_on": null,
    "created_at": "2021-05-24 10:11:39",
    "updated_at": "2021-05-24 10:11:39",
    "deleted_at": null,
    "version_master_id": null,
    "version_saved_at": null,
    "author": {
        "display_name": "Roxana1234",
        "avatar_url": "https://dzryyo1we6bm3.cloudfront.net/avatars/IMG_3636-1614176795-149628.jpg",
        "total_posts": 7,
        "days_as_member": 1392,
        "signature": null,
        "access_level": "admin",
        "xp": "250",
        "xp_rank": "Enthusiast II",
        "total_post_likes": 0,
        "created_at": "2017-07-31 22:54:41",
        "level_rank": "1.0"
    },
    "reply_parents": [
        {
            "id": 36631,
            "thread_id": 4,
            "author_id": 149628,
            "prompting_post_id": null,
            "content": " Uninstall using the Toolbox App? If you installed PhpStorm using the Toolbox App, do the following: Open the Toolbox App, click the screw nut icon for the necessary instance, and select Uninstall.",
            "state": "published",
            "published_on": "2021-05-24 07:49:21",
            "edited_on": null,
            "created_at": "2021-05-24 07:49:21",
            "updated_at": "2021-05-24 07:51:29",
            "deleted_at": null,
            "version_master_id": null,
            "version_saved_at": null
        }
    ]
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

### Update Post 

```
PATCH /post/api/update/{id}
```

#### Request Example

#### Permission required `update-posts`

```
var content = 'Nam sit delectus debitis consectetur.';

$.ajax({
    url: '/forums/api/post/update/{id}',
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

### Report Post
```
PUT /forums/api/post/report/{id}
```

#### Permission required `report-posts`

#### Request Example

```
var postId = 1;

$.ajax({
    url: '/forums/api/post/report/' + postId,
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
| path              | {id} | yes      |         | The post id to report |

#### Response Example

``` 204 ``` No content
```404``` when user does not have permission to report posts
``` 404 ``` when specified post does not exist


### Like Post
```
PUT /forums/api/post/like/{id}
```

#### Permission required `like-posts`

#### Request Example

```
var postId = 1;

$.ajax({
    url: '/forums/api/post/like/' + postId,
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
```404``` when user does not have permission to like posts
``` 404 ``` when specified post does not exist

### Unlike Post - JSON controller
```
DELETE /forums/api/post/unlike/{id}
```

#### Permission required `like-posts`

#### Request Example

```
var postId = 1;

$.ajax({
    url: '/forums/api/post/unlike/' + postId,
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

``` 204 ``` No content
``` 404 ``` when user does not have permission to like posts
``` 404 ``` when specified post does not exist


### Search
```
GET /forums/api/search
```

#### Request Example

```
var term = 'similique quidem dolorum suscipit eligendi';

$.ajax({
    url: '/forums/api/search',
    type: 'put',
    data: {term: term, page: 1, limit: 2},
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
| query             | page  | no       | 1       | The page of results to return                        |
| query             | limit | no       | 10      | The amount of results to return                      |
| query             | sort  | no       | score   | The column to sort results by                        |

#### Response Example

` 200 OK `
```json
{
    "status": "ok",
    "code": 200,
    "page": 1,
    "limit": "2",
    "total_results": 7705,
    "results": [
        {
            "id": 854,
            "thread_id": 45,
            "author_id": 151730,
            "prompting_post_id": null,
            "content": "<p>This is a suggestion, I think and in my opinion what to look-out for and be mindful of.  The <span style=\"text-decoration:underline;\"><strong>advantages and disadvantages</strong></span> will be more apperant when you have out-lived the use of the model of keyboard you are stuck with. You will find short-comings. For example,  external speaks to the Synthesized keyboard, most do come with speakers and some dont come with external speaks as they are more a MIDI instrutment. It is an attachment and additional cost-based item to enhance your keyboard. It lacks the speakers to hear your own playing. The head phone is an additional cost if you want to hear your self play the keyboard, while you are playing it. Hearing yourself play in real-time. </p>\n<p> </p>\n<p><span style=\"text-decoration:underline;\"><strong>Synthesized Keyboard versus Piano Keyboard (Ascoutic)</strong></span></p>\n<p>1: <strong>Price point</strong> for a Synthesized keyboard it is affordable, you get to start playing the piano on a budget.</p>\n<p>2: <strong>Keyboard length</strong> is more available on Synthesized keyboard, 25, 32, 37, 49. 61, and 88 keys. Personally, I learned it the hard way. I found out from a 61 keyboard that a normal Piano keyboard is 88 keys. lol. 8 Octaves as opposed 6 Octaves. The octaves will be important if you are playing up and down the length of the keyboard. As a beginner I still prefer 8 Octaves and 88 keys, So this is a choice-desicion. There is a <strong>sliding scale on cost</strong> to this.</p>\n<p>3: With synthesized keyboards, when you <strong>out-grown your keyboard</strong> you will have to change for those extra features you never had.</p>\n<p>4: Synthesized keyboard especially for a piano voicing, it is a <strong>sampled sound of an ascoutic Piano</strong> sound. Quality is close but not as close as the real thing: Grand Piano.</p>\n<p>5: <strong>Space over Ascoustic Piano</strong>, less space requirements, You can fit it on a table top or an x- stand.</p>\n<p>6: Synthesized keyboard comes with a number of <strong>voicing</strong>: instruments - Organ, E Piano, Guitar, etc</p>\n<p>7: Synthesized keyboard, they are more users (I think) then Ascoustic Piano users. Hence, there are common knowledge of  features on how to use the keyboard. It is a computer effectively. The Brand, for example Yamaha have Yamaha schools for you to go to and learn-off the Piano you have. Synthesized Piano Keyboard. You dont necessary have to own a keyboard until you experience what choices you have and what to look for.</p>\n<p>8: <strong>Touch, Feel and Responsiveness</strong> on a Grand Piano over Synthesized keyboard is very apparent. The keyboard keys on most synthesized keyboard come as a feature. It is a cost- based feature. Like weighted keys, graded hammer keys and semi-weighted keys you will find out the difference when you start to play the keyboard. You want weighted keys even though it is electronic like flicking a switch for a note sounding.</p>\n<p> </p>\n<p>I understand you want something of good sound quality. If that is the case than a Grand Piano really sounds good. It is ascoutic, It is not synthesized and the sound is from striking on real strings. It is not sampled. An upright piano is close. At least you do not have to tune-up your piano with an electronic synthesized keyboard. They just burn-out.</p>\n<p> </p>\n<p>Jordan did a video on this very question but I cant find it now and reference it now on here. I was going to post a link to it.</p>\n<p> </p>\n<p> </p>\n<p> </p>\n<p> </p>\n<p> </p>\n<p> </p>",
            "state": "published",
            "published_on": "2018-02-17 12:25:25",
            "edited_on": "2018-02-17 13:00:20",
            "created_at": "2018-02-17 12:25:25",
            "updated_at": "2018-02-17 13:00:20",
            "deleted_at": null,
            "version_master_id": null,
            "version_saved_at": null,
            "like_count": 0,
            "is_liked_by_viewer": 0,
            "author": {
                "display_name": "ReynoldUK",
                "avatar_url": "https://d2vyvo0tyx8ig5.cloudfront.net/avatars/1367_avatar_1517174437.jpeg",
                "total_posts": 354,
                "days_as_member": 1238,
                "signature": null,
                "access_level": "piano",
                "xp": 0,
                "xp_rank": "Casual",
                "total_post_likes": 237,
                "created_at": "2017-12-30 09:48:08",
                "level_rank": "1.0"
            },
            "thread": {
                "id": 45,
                "category_id": 1,
                "author_id": 151455,
                "title": "Choosing keyboard",
                "slug": "choosing-keyboard",
                "pinned": 0,
                "locked": 0,
                "state": "published",
                "post_count": 2,
                "last_post_id": 854,
                "published_on": "2017-10-26 17:10:36",
                "created_at": "2017-10-26 17:10:36",
                "updated_at": "2018-10-30 03:01:26",
                "deleted_at": null,
                "version_master_id": null,
                "version_saved_at": null,
                "category_slug": "general-piano-discussion",
                "category": "General Piano Discussion",
                "last_post_published_on": "2018-02-17 12:25:25",
                "last_post_user_id": 151730,
                "is_read": 0,
                "is_followed": 0,
                "mobile_app_url": "https://dev.pianote.com/forums/api/thread/show/45",
                "author_display_name": "Sebastian F",
                "author_avatar_url": "https://d2vyvo0tyx8ig5.cloudfront.net/avatars/1081_avatar_1508343341.jpeg",
                "author_access_level": "piano",
                "published_on_formatted": "Oct 26, 2017",
                "latest_post": {
                    "id": 854,
                    "created_at": "2018-02-17 12:25:25",
                    "created_at_diff": "3 years ago",
                    "author_id": 151730,
                    "author_display_name": "ReynoldUK",
                    "author_avatar_url": "https://d2vyvo0tyx8ig5.cloudfront.net/avatars/1367_avatar_1517174437.jpeg"
                }
            }
        },
        {
            "id": 2684,
            "thread_id": 177,
            "author_id": 152032,
            "prompting_post_id": 2679,
            "content": "<blockquote><span class=\"post-id\">4585</span><p class=\"quote-heading\"><strong>Jesus</strong><em> - Oct 8, 2018</em></p><br /><p>Is not bad at all Piot :) Im sure I will enjoy it further when I see you actually playing it on the piano. Yet I have several questions mate</p>\n<p> </p>\n<p>You say you are working on (for the video???) with the metronome. Did I understood you right? Cos if you played this without a metronome, to me sound it all in the beat and very accurate ritmically</p>\n<p> </p>\n<p>Also, how do you do it? Do you record everything in different channels of your keyboard and just hit \"play\" altogether or you record it all separate with a DAW and them mix it all?</p>\n<p> </p>\n<p>I wish you luck if you say will be your first video, me personally, anytime I try to record myself is like nothing goes wright due to know yourself recorded LOL - hope will not happens to you :)</p></blockquote><p>&nbsp;</p><p>Thank you Jesus :) There is the answer to your question:</p>\n<p> </p>\n<p>Well, I do not have only one way to do it, but I will try to reveal the basic usual process.</p>\n<p> </p>\n<p>First - I play something on piano, usually left hand chords and right hand the same chords, then on my right hand after I feel comfortable with progression I choose single notes from chords, add notes from scales double with octaves and create a melody that follows my chord progression. Then I change left hand patterns, try chord inversions, arpeggions etc. and listen what combination sounds best.</p>\n<p> </p>\n<p>Then I have several ideas from this improvisation and I connect my piano to my computer as a MIDI controller and record them splitted as short musical licks. I like Ableton software (Lite version is free) - it is the best, most powerful software to make songs in my opinion. I also have a little midi keyboard controller (novation launchkey 49 mk2) and it allowes me to work faster with the software, but I prefer playing on weighted keyboard keys on my piano. Synth keys are very light so the dynamics are kind of unpredictable, with weighted keys you have more control over dynamics.</p>\n<p> </p>\n<p>So I split everything into parts (right and left hand separetly) and record chunks of ideas and play them in different versions, listen to them and start asking myself questions like - does my melody sound better with plain chords or arpeggios beneath? Does itsound better an octave higher or lower? Faster or slower? What length that melody should be ? - is 16 measures boring, maybe 8 mesures are better? - with this chunks of ideas I create a song freamework and I play my piano on top of it - coming with some new ideas and then I repeat the process. Then I learn my final production on piano again and it usually requires to exercise it with metronome to nail it.</p>\n<p> </p>\n<p>When you play and when you just hear your playing it is a different experience, I find easier to compose a song structure when I just hear my recorded playing because my head is colder then ;-)</p>\n<p> </p>\n<p>With my piano working as MIDI I can also play on it with different sounds - strings, bass guitar, even drums. You have to know how Ableton works to fully understand this. With Novation launchkey MIDI controller I got pricey, commercial VST instrument called XLN Addictive Keys for free, so my MIDI piano sounds good even as MIDI.</p>\n<p> </p>\n<p>I am working with metronome because the song sound better in 80 bpm tempo - I speed it up in the software (that is called cheating, hehe ;-) ) - originally I was playing in 60 bpm tempo so I have to work up to 80 bpm to make the video of me playing the song in that tempo. Of course it is also possible to speed up the video but I do not want to do that beacuse learning to perform my music as is, gives me a satisfaction from my accomplishment. That is the reason I play piano afterall :-)</p>\n<p> </p>\n<p>About accuracy - there is an option called quantization - it just snaps notes to the grid for exaple eight notes - but overusing it results in robotic, unnatural rhythm, so it is always better to play it right with micro rhythm changes and sound as human :) <br />Mistakes are actually good I sometimes by mistake play a note outside the scale and it happens to sound great... and your mistakes make you sound as yourself, they make your unique sound.</p>\n<p> </p>\n<p>It may sound like a lot of work but it is actually not when you know your stuff - I am heavily into computers, I work in IT business so I have ease with computers... and doodling with music software is fun too, and gives you another angle of understanding how music works and even how piano works - I think that they complementing each other very well. I used to be very intolerant and I was avoiding making music with help of computer, I used to think that this is an inferior method and I was trapped in this little box of limitations when I started in my twenties. Now I am curious about everything that sounds good to me and do not limit myself to one particular method or workflow and I grow musically faster with that open attitude. And to be really good at playing piano you need years of playing, but music software is something you can learn in a month, so in a short timeframe you gain very powerful tool that can really aid your songwriting skills.</p>\n<p> </p>\n<p>I have started piano when I was 34, so I probably won't be another Chopin, but I believe that it is possible to write and play great songs even with very basic technical skills.</p>\n<p> </p>\n<p>Is there a better / faster way to compose songs? Yes - do everything on piano. But I unfortunately do not have such a technical piano skills to play everything I want, so I support myself with computer software. I hope someday I will have the skills to play everything I hear in my head on piano.</p>",
            "state": "published",
            "published_on": "2018-10-08 13:47:27",
            "edited_on": "2018-10-08 15:00:31",
            "created_at": "2018-10-08 13:47:27",
            "updated_at": "2018-10-09 16:03:12",
            "deleted_at": null,
            "version_master_id": null,
            "version_saved_at": null,
            "like_count": 1,
            "is_liked_by_viewer": 0,
            "author": {
                "display_name": "Piotr Sierant",
                "avatar_url": "https://s3.amazonaws.com/pianote/defaults/avatar.png",
                "total_posts": 70,
                "days_as_member": 1146,
                "signature": null,
                "access_level": "piano",
                "xp": 0,
                "xp_rank": "Casual",
                "total_post_likes": 0,
                "created_at": "2018-04-01 12:12:24",
                "level_rank": "1.0"
            },
            "thread": {
                "id": 177,
                "category_id": 1,
                "author_id": 149630,
                "title": "YOUR Compositions",
                "slug": "your-compositions",
                "pinned": 0,
                "locked": 0,
                "state": "published",
                "post_count": 38,
                "last_post_id": 16344,
                "published_on": "2018-07-13 15:53:34",
                "created_at": "2018-07-13 15:53:34",
                "updated_at": "2019-03-04 17:43:23",
                "deleted_at": null,
                "version_master_id": null,
                "version_saved_at": null,
                "category_slug": "general-piano-discussion",
                "category": "General Piano Discussion",
                "last_post_published_on": "2020-04-17 23:06:44",
                "last_post_user_id": 353089,
                "is_read": 0,
                "is_followed": 0,
                "mobile_app_url": "https://dev.pianote.com/forums/api/thread/show/177",
                "author_display_name": "Lisa Witt",
                "author_avatar_url": "https://d2vyvo0tyx8ig5.cloudfront.net/avatars/149630_1609278320825-1609278322-149630.jpg",
                "author_access_level": "admin",
                "published_on_formatted": "Jul 13, 2018",
                "latest_post": {
                    "id": 16344,
                    "created_at": "2020-04-17 23:06:44",
                    "created_at_diff": "1 year ago",
                    "author_id": 353089,
                    "author_display_name": "Doc322",
                    "author_avatar_url": "https://s3.amazonaws.com/pianote/defaults/avatar.png"
                }
            }
        }
    ],
    "filter_options": null
}
```

### Forum Rules


