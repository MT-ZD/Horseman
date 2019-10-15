# Horseman - a headless Wordpress theme

Horseman in a headles Wordpress theme with improved REST API.

The improvements include:

- Added thumbnail URL's with all sizes to `/posts` request
- Added author's name and avatar URL to `/posts` request
- Added post title and slug to `/comments` request

## Data structure

### Post thumbnail sizes (posts)

```json
{
  "image": {
    "thumbnail": "thumb-url",
    "medium": "thumb-url",
    "medium_large": "thumb-url",
    "large": "thumb-url",
    "post_thumbnail": "thumb-url",
    "full": "thumb-url"
  }
}
```

### Post author data (posts)

```json
{
  "author": {
    "id": 1,
    "name": "Author Name",
    "avatar": "avatar-url"
  }
}
```

### Post data (comments)

```json
{
  "post": {
    "id": 1,
    "title": "Post title",
    "slug": "post-slug"
  }
}
```

## Installation

1. Put folder with the files in `wp-content/themes` directory like any other Wordpress theme
2. Activate the theme in WP's admin panel
