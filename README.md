# Myc-Api-PHP

Scraping data as API From [TheYNC](https://theync.com) Website. Using Replit.

I just doing what I want. So, Sometimes code structure could be wrong.

# API Documentation

API url :
`https://mphp.kanye4112.repl.co/`

## General
For opening channels path

`/?url=https://......`

For load video content

`/?view=https://.....`

## Home
| Url Path | Description |
| --- | --- |
|`/` | The home path of API.|
|`/(number)`|Other Page of home path|

## Latest Update
| Url Path | Description |
| --- | --- |
|`/latest` | The latest update path of API.|
|`/latest/(number)`|Other Page of latest path|

## Channels
| Url Path | Description |
| --- | --- |
|`/channels` | Available category path. |

## Responses
### Home, Latest, UserUpload
```
{
  "active": 12, //current loaded page
  "pages": 22, //current loaded page could be increase when load more
  "data": [
    {
      "img":"Thumb Image",
      "title": "Content Title",
      "user": "User Name",
      "userpath": "Uploaded user's url",
      "isexternal": false,
      "isgold": false,
      "uploaded_date": "Upload Date",
      "link": "Content Link"
    }
  ]
}
```