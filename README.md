# Horseman - a headless Wordpress theme

Horseman is a headless Wordpress theme with improved REST API.

The improvements include:

- Added thumbnail URLs with all sizes to `/posts` request
- Added author's name and avatar URL to `/posts` request
- Added post title and slug to `/comments` request
- Added menus `/hm/v1/menus`

## Installation

1. Put folder with the files in `wp-content/themes` directory like any other Wordpress theme.
2. Activate the theme in WP's admin panel.

## Configuration

You can change active menu and home page in 'Settings' menu in admin panel.

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

### Menu data

```json
{
  "term_id": 1,
  "slug": "menu-slug",
  "name": "Menu name",
  "description": "Menu description",
  "parent": 0,
  "active": true,
  "count": 1,
  "items": [
    {
      "title": "Item title",
      "url": "item-url",
      "target": "_blank",
      "description": "Item description",
      "attr_title": "Hover title",
      "classes": ["additional-class"]
    }
  ]
}
```
