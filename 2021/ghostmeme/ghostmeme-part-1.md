# BDPA NHSCC 2021 Problem Statement (part 1)

> See also: [API documentation](https://hscc6xt8cqqf.docs.apiary.io)

A multimedia messaging app that "deletes" secret messages after a while isn't a
new idea, but Ghost, Inc. has partnered with BDPA to create _Ghostmeme_ anyway!
Your team won the contract to build the Ghostmeme messaging app where users can
send and receive picture messages known as "memes," keep them private, or add
them to semi-public "stories".

<details><summary>Summary of requirements (15 total)</summary>

The app supports two user types: guests and authenticated users. Users interact
with each other through the exchange of
[meme images and text](https://en.wikipedia.org/wiki/Internet_meme). Users can
like memes, share their favorite memes, comment on their friends' memes, and
share private memes that expire after a while.

The app has at least four views: _Chats_, _Stories_, _Notifications_, and
_Auth_. The Chats view is where users privately exchange direct memes with one
another. The Stories view is where users share their memes with their friend
groups and interact with the memes said groups share with them. The
notifications view lists various alerts to the user. The Auth view is used for
handling authentication as only authenticated users can access the other views
and create new memes.

Note that time-based data in the API is represented as the number of
milliseconds elapsed since January 1, 1970 00:00:00 UTC. See
[the API documentation](https://hscc6xt8cqqf.docs.apiary.io/#/introduction/tips-for-debugging)
for details. Further note that you **must** use the API to complete this problem
statement, including interacting with data from other chapters, though you may
consider a hybrid approach where you have your own database storing non-API
data.

</details>

> We're looking for feedback! If you have any opinions or ideas,
> [start a discussion](https://github.com/nhscc/problem-statements/discussions/new).

## Requirement 1

**Your app will support 2 types of users: guest and authed.**

### Guest

- Are unauthenticated users (i.e. users that have not logged in)
- Can only access the [Auth view](#requirement-6)
- Can **NOT** view any memes in the system

### Authed (Authenticated)

- Are authenticated users (i.e. users that have already logged in)
- Can access every view
- Can only view memes created by themselves, memes sent via
  [chats](#requirement-2), or memes [shared by their friends](#requirement-6)
  [via stories](#requirement-3)
- Can **NOT** view [vanished memes or comments](#vanishing-memes)

## Requirement 2

**Chats view: create, send, and receive memes directly and privately.**

From this view, users can send "memes" directly to one another.

Memes in the system:

- Are objects that include either 1) an image url, 2) a description between 1
  and 500 characters, or both
  - When creating a new meme, users are asked for an image url. This means users
    are not _uploading_ their memes to your app, only linking to images hosted
    elsewhere
  - A description is always optional if an image url is provided
- Are rendered as an image (if given)
  - If there is a description (i.e.
    [`description`](https://hscc6xt8cqqf.docs.apiary.io/#/data-structures/0/meme)
    property is _non-null_), it is displayed near the image or by itself if
    there is no image
  - This means meme objects with a _null_
    [`imageUrl`](https://hscc6xt8cqqf.docs.apiary.io/#/data-structures/0/meme)
    property and _non-null_
    [`description`](https://hscc6xt8cqqf.docs.apiary.io/#/data-structures/0/meme)
    property should render as a simple chat message in this view
- Cannot be edited after creation
  - The only exception is when [manually vanishing a meme](#vanishing-memes)
- Usually appear in a scrollable list, colloquially referred to as a "wall,"
  "feed," or "chat"
- Show the username of the user that owns/created them
- Show some sort of timestamp describing when they were created
- Can be assigned an [_expiration time_](#vanishing-memes) upon creation

Memes should be displayed in descending creation order (i.e. newest first).

<blockquote>

Meme objects created for this view must have a valid `user_id` for the
[`receiver`](https://hscc6xt8cqqf.docs.apiary.io/#/data-structures/0/meme)
property, a _true_
[`private`](https://hscc6xt8cqqf.docs.apiary.io/#/data-structures/0/user)
property, and a _null_
[`replyTo`](https://hscc6xt8cqqf.docs.apiary.io/#/data-structures/0/meme)
property. This is the difference between meme objects displayed in the Chats
view and meme objects meant for display in other views.

</blockquote>

### Vanishing memes

When sending a meme to another user, they can give the meme an _expiration time_
(stored as
[`expiredAt`](https://hscc6xt8cqqf.docs.apiary.io/#/data-structures/0/meme))
representing some point in the future. Once this time elapses, the meme will no
longer appear in the Chats view. This is referred to in the UI as a "vanishing,"
"vanished," or "ghost" 👻 meme.

The app will **never** show ghost memes. That is: if
`0 ≤ expiredAt < Date.now()`, the meme will not appear in the Chats view (or
anywhere else). However, there should be some visual indication in the UI that a
particular meme has vanished from this view.

Users can also manually vanish any previously sent memes. A meme can be manually
vanished by its owner at any point, and even if the meme originally had no
expiration time; i.e., originally `expiredAt ≤ -1`, but `expiredAt == 0` after
being manually vanished.

> Set a meme object's
> [`expiredAt`](https://hscc6xt8cqqf.docs.apiary.io/#/data-structures/0/meme)
> property to `0` ensure it is vanished. On the other hand, when
> [`expiredAt`](https://hscc6xt8cqqf.docs.apiary.io/#/data-structures/0/meme) is
> set to `-1` (or any negative number), the meme should _never_ automatically
> vanish and should _always_ be shown in this view.

## Requirement 3

**Stories view: like, share, and comment on memes with
[friends](#requirement-6).**

From this view, users can:

- View all the memes they've shared to their personal "story"
  - Each user has a single "story" they can add memes to
  - The contents of a user's story are only available to that user and their
    [friends](#requirement-6)
- View 🔎, like ❤️, and comment 🗣️ on memes added to their own story and to
  their [friends'](#requirement-6) stories
  - Comments are meme objects created in direct response to a meme _shared to a
    user's story_
    - This means comments cannot be created in response to other comments
    - This also means the _text_ of a comment is stored in the
      [`description`](https://hscc6xt8cqqf.docs.apiary.io/#/data-structures/0/meme)
      property
  - Like other meme objects in the system, comments should be rendered as images
    if the
    [`imageUrl`](https://hscc6xt8cqqf.docs.apiary.io/#/data-structures/0/meme)
    property is not _null_
- "Manually vanish" (delete) memes they've shared to their story and comments
  they've made
  - Unlike in the [Chats view](#requirement-2), meme objects in the Stories view
    don't expire on a timer; however, they can be
    _[manually vanished](#vanishing-memes)_ (deleted) by the user
  - To indicate that a meme or comment has been manually vanished, set its
    [`expiredAt`](https://hscc6xt8cqqf.docs.apiary.io/#/data-structures/0/meme)
    property to `0`
  - **Vanished comments must not be visible in the in the Stories view**
- Share a meme they saw on a friend's story directly to their own story

Memes should be displayed in descending creation order (i.e. newest first).

<blockquote>

Meme objects created for this view must have a _null_
[`receiver`](https://hscc6xt8cqqf.docs.apiary.io/#/data-structures/0/meme)
property and a _true_
[`private`](https://hscc6xt8cqqf.docs.apiary.io/#/data-structures/0/user)
property. If it's a new meme upload,
[`replyTo`](https://hscc6xt8cqqf.docs.apiary.io/#/data-structures/0/meme) must
also be _null_. If instead it's a new comment in response to a meme upload,
[`replyTo`](https://hscc6xt8cqqf.docs.apiary.io/#/data-structures/0/meme) must
be a valid `meme_id`. This is the difference between meme objects displayed in
the Stories view and meme objects meant for display in other views.

</blockquote>

## Requirement 4

**Notifications view: manage various informational alerts.**

This view shows the user a list of all the memes from their
[Stories view](#requirement-3) that have descriptions or comments that mention
them. Additionally, users can dismiss notifications they no longer wish to see.

To fully satisfy this requirement, users of _your_ app always receive a
notification about being mentioned unless they already dismissed it through
_your_ app. That is: teams may find it necessary to _rebuild_ the local data of
existing users (perhaps created through _other_ frontends) when they first
login.

> The API does not store user notification data. That is the responsibility of
> each individual team's solution. However, the API contains all of the data
> necessary to reconstruct all of a user's recent notifications.

## Requirement 5

**Auth view: user registration and login.**

`guest` users can use this view to login using their _username or email_ and
their _password_. There is an open registration feature guests can use to
register a new account. When they do, they must provide the following where
required:

- Full name <sup>\<required\></sup>
- Phone number
- Email address <sup>\<required\></sup>
- Password <sup>\<required\></sup>
  - Password strength must be indicated as well. Weak passwords will be
    rejected. A weak password is ≤10 characters. A moderate password is 11-17
    characters. A strong password is above 17 characters.
- [Profile picture file](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input/file)
  <sup>\<required\></sup>
- The answer to a simple CAPTCHA challenge of some type <sup>\<required\></sup>
  - Example: `what is 2+2=?`
  - Teams must not call out to any API for this. Teams must create the CAPTCHA
    manually.

When registering, the user must choose a picture from their computer and
[upload](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input/file)
it to your app as their profile picture. Even though your team is storing and
indexing the images locally, profile picture uploads should be limited to a
reasonable file size and dimensions.

> Unlike past problem statements, user creation/deletion is managed for you
> through
> [the API](https://hscc6xt8cqqf.docs.apiary.io/#/reference/0/user-endpoints/users-post).
> However, dealing with login credentials, storing uploaded profile pictures,
> and managing other non-API data is still your team's responsibility.

To fully satisfy this requirement:

- There should be some way to logout of the app
- The absolute maximum image size must be ≤10MB (you should choose a lower
  limit)
- Allowed image file types must be limited to: JPEG, PNG, and GIF
- Guests will be prevented from logging in for 5 minutes after 3 failed login
  attempts. Guests will always see how many attempts they have left.
- Guests will have the option to use
  [remember me](https://www.troyhunt.com/how-to-build-and-how-not-to-build)
  functionality to maintain long-running authentication state. That is: if
  guests login and want to be "remembered," they will not be logged out until
  they manually log out.

## Requirement 6

**`authed` users can send and accept friend requests and unfriend other users.**

Users can "friend" and "unfriend" other users they encounter in the app by
exchanging friend requests. For example, when user _A_ sends a friend request to
user _B_ and user _B_ accepts that request, user A's memes appear in user B's
[Stories view](#requirement-3) and vice-versa. Conversely, when one user
unfriends another, each user should immediately stop seeing the others' memes in
their respective [Stories view](#requirement-3).

To fully satisfy this requirement, users must be able to:

- View a list of their friends and friend requests
- Remove friends
- Send new friend requests by username, phone, email or by interacting with
  [mentions](#requirement-8)
  - This is the _outgoing_ friend request type
- Accept and reject friend requests sent to them
  - This is the _incoming_ friend request type

Additionally, users will be [notified](#requirement-4) when they have
outstanding incoming friend requests.

Teams can create a separate view for friendship functionality or merge it into
an existing view.

> The API only stores information about friendships and outstanding requests
> between users. It will not stop your app from adding/removing friends or
> adding/removing requests in strange and nonsensical ways. It is up to your
> team to implement the business logic of friend management while also handling
> potentially bad data sent by other teams.

## Requirement 7

**If a user does not remember their password, they can use email to recover
their account.**

If a user has forgotten their login credentials, they will be able to recover
their account by clicking a link in the recovery email sent to their address.

> The app must not actually send emails out onto the internet. The sending of
> emails can be simulated however you want, including outputting the would-be
> email to the console. The app will not use an external API or service for
> this. **For full points, ensure you document how recovery emails are simulated
> when submitting your solution so judges can test it.**

## Requirement 8

**`authed` users can use _hashtags_ and _mention_ other users in comments and
meme descriptions.**

Users can "mention" other users in meme descriptions and comments by inputting
an `@` followed by the target user's username, e.g.
`This meme was inspired by @username!!!` mentions user `username`. If the user
does not exist, it's just normal text. If the user does exist, the text becomes
a _mention_, which is rendered as a link allowing other users to view the
mentioned user's name and profile picture
[and add/remove them as a friend](#requirement-6). Note that
[this does not have to occur in a separate view](https://atomiks.github.io/tippyjs/#html-content).

Users can also include _hashtags_, e.g. `#MyHashTag`, in meme descriptions and
comments by inputting a `#` followed by a string of alphanumeric characters.
Each hashtag will be rendered as a link pointing to its own
[search result](#requirement-9).

Additionally, users will be [notified](#requirement-4) when they are mentioned
in a new comment or newly uploaded meme's description they have permission to
view.

## Requirement 9

**`authed` users can search for memes in the system.**

Users can search for memes by their _description_, by _hashtags_ in the
description, by _owner_, by _creation time_, or some combination thereof.
Results can then be sorted by _owner_ or _creation time_. Users can access this
functionality directly from any view in the app.

To fully satisfy this requirement, results should:

- Be visually divided into two tabs/sections: _results from chats_ and _results
  from stories_
- Render each meme's image, owner, creation time, and description
- Only include memes the user has permission to see<sup>1</sup>
- Be ordered by descending creation time initially (i.e. newest memes first)
- Be sortable by the criteria outlined above

<sub><sup>1</sup> Currently,
[users only have permission to see memes from their friends' stories](#authed-authenticated).
Showing user _X_ a meme from user _Y_'s story when _X_ and _Y_ are not friends
is an egregious privacy violation and security vulnerability.</sub>

## Requirement 10

**Like counts will update in the UI in real time; new comments on memes will
appear without a page refresh.**

In the [Stories view](#requirement-3), updates to a meme's like count and new
comments must appear in the UI as they happen _without the page refreshing_ or
the user doing anything extra, like pressing a refresh button. In the
[Chats view](#requirement-2), new meme objects should appear as they are
received.

<blockquote>

This type of automatic updating is called _asynchronous_ or
"[ajaxian](<https://en.wikipedia.org/wiki/Ajax_(programming)>)" since it occurs
outside the usual
[_synchronous_ event flow](https://dev.to/lydiahallie/javascript-visualized-event-loop-3dif).
There are
[many](https://www.encodedna.com/javascript/practice-ground/default.htm?pg=auto-refresh-div-using-javascript-and-ajax)
[examples](https://www.internetlivestats.com/one-second/#tweets-band). One way
to implement asynchronous features (and some forms of caching) is by using
[frontend timers](https://developer.mozilla.org/en-US/docs/Learn/JavaScript/Asynchronous/Timeouts_and_intervals)
to regularly check the API for new data every now and then.

</blockquote>

## Requirement 11

**The app will be performant.**

[The amount of time the application takes to load and sort data, display information, and act on input is progressively penalized](https://www.websitebuilderexpert.com/building-websites/website-load-time-statistics).
That is: the teams with the fastest (lowest) load times will earn the most
points and the teams with the slowest (highest) load times will earn zero points
from this requirement.

Average (median) is used to calculate load times. Measurements will include
initial page load times and, depending on the other requirements, various other
frontend UI response times.

> [FOUC](https://petrey.co/2017/05/the-most-effective-way-to-avoid-the-fouc) may
> also be penalized.
> [Tail latencies](https://robertovitillo.com/why-you-should-measure-tail-latencies)
> (e.g. on startup with a
> [cold cache](https://stackoverflow.com/questions/22756092/what-does-it-mean-by-cold-cache-and-warm-cache-concept))
> are ignored; only averages are considered.

> To maximize performance, consider
> [caching](https://www.sitepoint.com/cache-fetched-ajax-requests)
> ([see also](https://developer.mozilla.org/en-US/docs/Web/HTTP/Caching)) the
> result of data processing operations and using
> [range queries](https://hscc6xt8cqqf.docs.apiary.io/#/introduction/pagination)
> to retrieve only the data your app hasn't yet processed.
> [**Fetching results from a local cache can be upwards of 100x faster**](https://www.peterbe.com/localforage-vs-xhr/index.html)
> than making an API request for the same data, plus you avoid hitting rate
> limits. However, care must be taken to keep the cache
> [fresh](https://developer.mozilla.org/en-US/docs/Web/HTTP/Caching#freshness).

## Requirement 12

**Results and lists of items displayed in the frontend UI will be paginated
where appropriate.**

[Pagination](https://www.smashingmagazine.com/2007/11/pagination-gallery-examples-and-good-practices)
is the strategy of showing a limited number of a large set of results and
providing a navigation element where users can switch to different "pages" of
that large set. A Google search result (which has multiple pages) is a good
example of pagination.
[Infinite scroll](https://infinite-scroll.com/demo/full-page), a specific
pagination implementation used by the likes of Facebook/Instagram and Twitter,
is another good example.

## Requirement 13

**Security: no [XSS](https://owasp.org/www-community/attacks/xss),
[SQLI](https://owasp.org/www-community/attacks/SQL_Injection),
[insecure database](https://haveibeenpwned.com), or
[other trivial security vulnerabilities](https://owasp.org/www-project-top-ten).**

The app will use modern software engineering practices that protect from common
[XSS](https://owasp.org/www-community/attacks/xss),
[SQL injection](https://owasp.org/www-community/attacks/SQL_Injection), and
[other security vulnerabilities](https://owasp.org/www-project-top-ten).
Specifically: **form inputs** and the like **will not be vulnerable to SQL
injection attacks. User-generated outputs will not be vulnerable to XSS or
similar attacks.**

Additionally, any passwords stored in local or remote databases
[must](https://auth0.com/blog/hashing-passwords-one-way-road-to-security)
[be](https://codahale.com/how-to-safely-store-a-password)
[protected](https://cheatsheetseries.owasp.org/cheatsheets/Password_Storage_Cheat_Sheet.html).
Your language of choice
[has](https://docs.oracle.com/en/java/javase/11/docs/api/java.base/javax/crypto/SecretKeyFactory.html)
[built](https://www.php.net/manual/en/function.password-hash.php)-[in](https://nodejs.org/api/crypto.html#crypto_crypto_scrypt_password_salt_keylen_options_callback)
[tools](https://developer.mozilla.org/en-US/docs/Web/API/SubtleCrypto/deriveKey#pbkdf2)
to handle all this for you, and
[there](https://www.baeldung.com/java-password-hashing)
[are](https://www.php.net/manual/en/faq.passwords.php#faq.passwords.bestpractice)
[many](https://stackoverflow.com/a/61405208/1367414)
[tutorials](https://8gwifi.org/docs/window-crypto-pbkdf.jsp) for how to safely
store passwords in a database.
[Your users are counting on you to protect them!](https://www.statista.com/statistics/273550/data-breaches-recorded-in-the-united-states-by-number-of-breaches-and-records-exposed/)

> When storing secrets, we recommend using SHA256+bcrypt, scrypt, or PBKDF2
> depending on your language (and regardless of frontend vs backend). Secrets
> stored in [cleartext](https://simple.wikipedia.org/wiki/Cleartext) or just
> re-encoded, e.g. with
> [base64](https://base64.guru/blog/base64-encryption-is-a-lie), will earn your
> team zero points for this requirement.

## Requirement 14

**The app will
[fail gracefully](https://getbootstrap.com/docs/5.0/forms/validation/#server-side)
when exceptional conditions are encountered.**

This includes handling API errors during fetch, login errors, random exceptions,
[showing spinners](https://getbootstrap.com/docs/4.4/components/spinners) when
content needs to load, etc.

> Every so often,
> [the API will respond with an `HTTP 555` error](https://hscc6xt8cqqf.docs.apiary.io/#/introduction/rules-of-api-access)
> instead of fulfilling a request. Further, API requests and responses will be
> manipulated by the judges in an attempt to break the app. If at any time a
> user is presented with a _non-app_ error page or a completely blank screen for
> [more than a second or so](https://www.websitebuilderexpert.com/building-websites/website-load-time-statistics),
> your solution may lose points on this requirement.

## Requirement 15

**The frontend UI will be responsive to mobile, tablet, and desktop
[viewports](https://www.w3schools.com/css/css_rwd_viewport.asp).**

The app will be pleasant to the eye when viewed on a smartphone, tablet, and a
desktop viewport. The design and functionality will not "break" across these
viewports nor will the app become non-functional.

> Judges will view and interact with the app through
> [emulated phone and tablet viewports](https://developers.google.com/web/tools/chrome-devtools/device-mode).
> If the app breaks when viewed, it will lose points on this and other
> requirements. We recommend using
> [mobile-first software design principles](https://designshack.net/articles/mobile/mobilefirst).
