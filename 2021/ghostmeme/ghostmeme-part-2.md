# BDPA NHSCC 2021 Problem Statement (part 2)

> See also: [API documentation](https://hscc6xt8cqqf.docs.apiary.io)

Ghost, Inc. applauds the successful rollout of their "secret" messaging app!
Feedback indicates users are satisfied with your app UX and performance data
shows response time tail latencies are very low. But your contractor has
identified some changes they want implemented.

> We're looking for feedback! If you have any opinions or ideas,
> [start a discussion](https://github.com/nhscc/problem-statements/discussions/new).

> All uploaded images can be viewed [here](https://imgur.com/a/TytqlvJ).

There are ten (10) changes in total. Three (3) ✨changes✨ are worth more points
than the others:

## ✨Change 1✨

**Image file storage has been offloaded to the new API v1.1.0.**

A new version of the Ghostmeme API is available: version v1.1.0 🎉. This
backwards-compatible update officially allows the
[`POST /memes`](https://hscc6xt8cqqf.docs.apiary.io/#/reference/0/meme-endpoints/memes-post),
[`POST /users`](https://hscc6xt8cqqf.docs.apiary.io/#/reference/0/user-endpoints/users-post),
and
[`PUT /users/:user_id`](https://hscc6xt8cqqf.docs.apiary.io/#/reference/0/user-endpoints/users-user-id-put)
endpoints to recognize the `imageBase64` parameter. This parameter takes a
[base64-encoded string](https://stackoverflow.com/questions/10315757/what-is-the-real-purpose-of-base64-encoding)
representing an image file.

Previously, profile image files were stored locally. **With API v1.1.0, your app
is no longer allowed to store _any_ uploaded image files locally.** In this way,
the API becomes the single source of truth for all non-static images in the app.

Going forward, user profile image urls must be retrieved from the API using
[`GET /users/:user_id`](https://hscc6xt8cqqf.docs.apiary.io/#/reference/0/user-endpoints/users-user-id-get)
and the newly recognized
[`imageUrl`](https://hscc6xt8cqqf.docs.apiary.io/#/data-structures/0/user)
property. Retrieving meme image urls via the
[`imageUrl`](https://hscc6xt8cqqf.docs.apiary.io/#/data-structures/0/meme)
property has not changed.

> Note that API requests are limited to 10MB, including image uploads. Base64
> can bloat an image file's size by up to 33%, so plan your app's upload limits
> accordingly.

> Teams' solutions not using the API to store and retrieve profile images or
> meme files will earn zero points from this and other changes.

## ✨Change 2✨

**Authed users can upload image files directly when creating memes and choosing
profile images.**

Previously, users could only "upload" meme images by copying and pasting URLs.
With this change, users will have a second option: actually uploading their
image file directly from their PC. When an image is uploaded, the app must send
it to the API to be stored. This can be accomplished using the newly recognized
`imageBase64` parameter on the
[`POST /memes`](https://hscc6xt8cqqf.docs.apiary.io/#/reference/0/meme-endpoints/memes-post)
endpoint.

On the other hand, profile image files were already uploaded to your app, but
they weren't globally available across teams' solutions. With this change, user
profile images are now uploaded to the API **exclusively** via the `imageBase64`
parameter on the
[`POST /users`](https://hscc6xt8cqqf.docs.apiary.io/#/reference/0/user-endpoints/users-post)
and
[`PUT /users/:user_id`](https://hscc6xt8cqqf.docs.apiary.io/#/reference/0/user-endpoints/users-user-id-put)
endpoints. Your app is no longer allowed to store any uploaded images locally
(unless caching).

[Here is a JavaScript example](https://jsbin.com/piqiqecuxo/1/edit?js,console,output)
that 1) allows a user to select an image file and then 2) transforms that file
into a
[base64-encoded](https://stackoverflow.com/questions/10315757/what-is-the-real-purpose-of-base64-encoding)
[data URI](https://developer.mozilla.org/en-US/docs/Web/HTTP/Basics_of_HTTP/Data_URIs)
string. This is the string your app will upload to the API via the `imageBase64`
parameter described above. All images uploaded to the API can be viewed
[here](https://imgur.com/a/TytqlvJ).

## Change 3

**Authed users can change their account information and credentials.**

With this change, it is required that users can update their password, profile
picture, email, and/or phone number. Previously, this was not required
functionality.

A user must re-authorize with their password before being allowed to change
their account information or credentials.

Additionally, profile pictures are now optional for users and are no longer
required at registration.

## Change 4

**New toggleable website theme: dark mode.**

> Or, if your app UI already uses light text on a dark background, it needs a
> _light mode_!

The users have spoken and they want the ability to
[toggle the website to a dark version of itself](https://codepen.io/adhuham/pen/GRJxpQr),
dubbed _dark mode_. While the UI normally features blackish text on a bright
background, when in dark mode the UI will render white text on a dark
background.

## Change 5

**Hashtags are displayed along with their trend data.**

With this change, the app will start tracking the most frequently used hashtags
in the system and rank them with the most popular most used hashtag being "rank
1" and the least used hashtag "rank N" (of N). A hashtag's rank will be rendered
in the UI next to its text.

For example, suppose the hashtag `#MyTag` is the 5492th most used hashtag in the
system. When used in a meme description or comment, it could appear in the UI
looking something like the following:

> Blah blah witty comment. [**#MyTag** `5492`](#change-5) plus some more comment
> text.

## ✨Change 6✨

**New Spotlight view: interact with _public_ memes and trending hashtags.**

Up until this point, memes have been created as
[`private`](https://hscc6xt8cqqf.docs.apiary.io/#/data-structures/0/user)`== true`.
With this change, users now have the option to share memes _publicly_ in
addition to stories and chats. These public memes are listed in a new _Spotlight
view_ where both `authed` (logged in) and `guest` (not logged in) users can view
them. Only `authed` users can like or share public memes from this view. Unlike
memes shared to the Stories view, users _cannot_ comment on memes in the
Spotlight view; these memes can only be viewed and—by `authed` users—liked,
unliked, and shared to their user story.

At the top of the UI, the Spotlight view will show
[the top five trending hashtags in the system along with their trend data](#change-5).
Below that, a paginated list of the "most likable" publicly-shared memes in the
system is shown. Use or devise any reasonable algorithm to achieve this.

<blockquote>

Meme objects created for this view must have _null_
[`receiver`](https://hscc6xt8cqqf.docs.apiary.io/#/data-structures/0/meme) and
[`replyTo`](https://hscc6xt8cqqf.docs.apiary.io/#/data-structures/0/meme)
properties and a _false_
[`private`](https://hscc6xt8cqqf.docs.apiary.io/#/data-structures/0/user)
property. This is the difference between public meme objects displayed in the
Spotlight view and private meme objects meant for display in other views.

</blockquote>

## Change 7

**Authed users can send memes to multiple recipients at once.**

Previously, users could send a meme via a
[chat](https://github.com/nhscc/problem-statements/blob/main/2021/ghostmeme/ghostmeme-part-1.md#requirement-2)
with another user. With this change, users now have the UI option to send a
single meme to one _or more_ chats all at once instead of having to send it to
each user one by one.

## Change 8

**Authed users are notified when unread memes they've received are about to
vanish.**

With this change, the user will receive a
[notification](https://github.com/nhscc/problem-statements/blob/main/2021/ghostmeme/ghostmeme-part-1.md#requirement-4)
when a meme
[they were sent by another user](https://github.com/nhscc/problem-statements/blob/main/2021/ghostmeme/ghostmeme-part-1.md#requirement-2)
is one (1) minute or less away from
[vanishing](https://github.com/nhscc/problem-statements/blob/main/2021/ghostmeme/ghostmeme-part-1.md#vanishing-memes)
and has not been viewed yet using your app. Once the meme vanishes, this
notification should be deleted.

## Change 9

**Authed users can block and unblock other users.**

With this change, when a user sees a meme they don't like, they have the option
to **block** the user that created it. Blocked users' memes, chats, and comments
are never shown to the user that blocked them and vice-versa. Blocking a friend
automatically unfriends them. Friend requests from blocked users are silently
discarded.

There will be a way for the user to see a list of the users they've blocked and
to selectively unblock them. The ability to scroll a list of blocked users and
unblock some of them can be merged into an existing view if appropriate.

> The API does not store data on which users block which others. That is the
> responsibility of each individual team's solution.

## Change 10

**Authed users are notified when security events occur.**

The app will now keep track of security events and generate
[notifications](https://github.com/nhscc/problem-statements/blob/main/2021/ghostmeme/ghostmeme-part-1.md#requirement-4)
when they occur.

Security events include:

- When the user logs in and from which IP address
- When the user changes their email, phone number, profile picture, or password

With this change, the user will receive a
[notification](https://github.com/nhscc/problem-statements/blob/main/2021/ghostmeme/ghostmeme-part-1.md#requirement-4)
when one of these security events occur.
