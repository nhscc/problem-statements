# BDPA NHSCC 2021 Problem Statement (part 1)

> See also: [API documentation](https://hscckhug3eb6.docs.apiary.io)

With all the money to be made selling information, BDPA Media Conglomerate
decided to create a microblogging and social networking service called _Barker_!
Your team has been contracted to build the Barker platform, where users will
interact with each other through immutable 280-character text messages known as
"barks".

<details><summary>Summary of requirements (15 total)</summary>

<!-- brief brief summary here -->

The app supports two user types: guests and authenticated users. Users interact
with each other through immutable 280-character messages known as _Barks_. Users
can like, share, reply to, bookmark, and otherwise interact with each other's
Barks.

The app has at least four views: _Home_, _Pack_, _Bookmark_, and _Auth_. The
Home view is the primary view and is the gateway to the Barker platform. Pack
view is a specialized version of the Home view. Bookmark view is like Pack view
except with a unique sorting order. The Auth view is used for handling
authentication as only authenticated users can access the Pack and Bookmark
views and create new Barks.

Note that time-based data in the API is represented as the number of
milliseconds elapsed since January 1, 1970 00:00:00 UTC. See
[the API documentation](https://hscckhug3eb6.docs.apiary.io/#/introduction/tips-for-debugging)
for details. Further note that you **must** use the API to complete this problem
statement, including interacting with data from other chapters, though you may
consider a hybrid approach where you have your own database storing non-API
data.

</details>

> We're looking for feedback! If you have any opinions or ideas,
> [start a discussion](https://github.com/nhscc/problem-statements/discussions/new).

## Requirement 1

**Your app will support 2 types of users: guest and authed.**

### Guests

- Are unauthenticated users (i.e. don't have to login)
- Can access the [Home view](#requirement-2)
- Can access the [Auth view](#requirement-6)

### Authed (Authenticated)

- Are authenticated users (i.e. users that have already logged in)
- Can access every view

## Requirement 2

**Home view: create and interact with Barks from
[followed users](#requirement-8).**

- "Barks" are 1 to 280 character messages that cannot be edited after creation.
- Barks appear in a scrollable list, colloquially referred to as a "wall" or
  "feed".
- Clicking a Bark will
  [change the browser URL](https://developer.mozilla.org/en-US/docs/Web/API/History_API/Working_with_the_History_API)
  to point to the clicked Bark; each Bark will be reachable via its unique URL.
- Navigating to a Bark's URL in the browser will show the user that specific
  Bark. How to implement users seeing/scrolling to a specific Bark after
  entering its URL is up to your team.
- Each Bark shows the name of the user that created it and some way for others
  to [follow](#requirement-8) them.
- `authed` users are shown Barks only from the users they
  [follow](#requirement-8). They also see their own Barks. On the other hand,
  `guest` users are shown _all Barks in the system_.
- An `authed` user can create new Barks and delete (called **"rescind"** in the
  UI) old Barks.
  - Barks can be created normally or as a reply under another Bark (called a
    **"bark-back"** in the UI).
  - When a Bark is "deleted," it is not removed from the system. Instead,
    deleted/rescinded Barks have their
    [`deleted` flag set in the API](https://hscckhug3eb6.docs.apiary.io/#/data-structures/0/bark);
    this will be reflected in the UX.
- An `authed` user can interact with a Bark by **"liking"** it, sharing it
  (called a **"rebark"** in the UI), replying to it with a bark-back, and
  [bookmarking](#requirement-5) it.
  - Rebarking is the same as posting a new Bark, except the contents are copied
    from a pre-existing Bark.
  - A bark-back is like posting a new Bark, except it's directly in response to
    another Bark. **Bark-backs _cannot_ be created in response to other
    bark-backs!**
- Each Bark shows the total number of likes ❤️, rebarks 📢, and bark-backs 🐺
  that are associated with it.
- Each Bark shows some sort of timestamp describing when it was created.

Each Bark will be displayed in the order described below.

### Display Order of Barks

<blockquote>

Social media and e-commerce corporations are spending billions of dollars
crunching exabytes of data developing complex algorithms that
[still](https://lifehacker.com/how-to-see-recent-tweets-first-on-android-1831850131)
[fail](https://www.gizmodo.com.au/2020/08/instagrams-new-feed-update-ruins-your-endless-scroll)
[to](https://www.engadget.com/facebook-news-feed-chronological-order-121710029.html)
[answer](https://techcrunch.com/2017/11/29/snapchat-redesign) "which X is my
user most likely to like," but we'll give it our best shot.

</blockquote>

Ideally, we would show the user the Barks they are most likely to like. This
way, they stay scrolling in the app, maybe even becoming addicted to our
content. The challenge is accomplishing this with only
[_a priori knowledge_](https://en.wikipedia.org/wiki/A_priori_and_a_posteriori).
That is: we don't actually know which Barks the user will like until we observe
them pressing the like button, but we can develop an algorithm that makes a
decent guess.

This algorithm must sort the list of Barks such that the "most likeable" Barks
are displayed first. We define a Bark's relative "likeability" as some
combination of _the recentness of a Bark_ (users are more likely to be
interested in the latest content) and _the number of likes a Bark has received_
(things a lot of people like are more likely to be liked by the user too).

#### Barker Sorting Algorithm

Barks will first be grouped by creation time in one hour intervals. For example,
yesterday's barks between 12:00 AM and 1:00 AM should be in the same group, and
yesterday's barks between 1:00 AM and 2:00 AM should be in another group, etc.,
and today's barks between 12:00 AM and 1:00 AM should be in another group, and
so on.

Within each interval group, Barks should be sorted by number of likes first then
creation time second, both in descending order. For example, given the following
(unsorted) Barks from followed users A, B, and C:

```json
{ "owner": "C", "content": "Bark Bark Bark", "createdAt": 3619, "likes": 0 },
{ "owner": "C", "content": "Bark Bark", "createdAt": 3615, "likes": 1 },
{ "owner": "B", "content": "Hello world", "createdAt": 3610, "likes": 3 },
{ "owner": "A", "content": "Third bark!", "createdAt": 6, "likes": 2 },
{ "owner": "A", "content": "First bark!", "createdAt": 1, "likes": 1 },
{ "owner": "A", "content": "Second bark", "createdAt": 3, "likes": 20 },
{ "owner": "C", "content": "Bark", "createdAt": 5, "likes": 1 }
```

After grouping them by one hour intervals:

> In this example, 0-3599 seconds is one group and 3600-7199 seconds is another
> group.

```json
{ "owner": "C", "content": "Bark Bark Bark", "createdAt": 3619, "likes": 0 },
{ "owner": "C", "content": "Bark Bark", "createdAt": 3615, "likes": 1 },
{ "owner": "B", "content": "Hello world", "createdAt": 3610, "likes": 3 }
```

```json
{ "owner": "A", "content": "Third bark!", "createdAt": 6, "likes": 2 },
{ "owner": "A", "content": "First bark!", "createdAt": 1, "likes": 1 },
{ "owner": "A", "content": "Second bark", "createdAt": 3, "likes": 20 },
{ "owner": "C", "content": "Bark", "createdAt": 5, "likes": 1 }
```

Finally, after sorting within those groups and recombining them, the Home view
displays the Barks to the user in descending order of "most likeable":

```json
{ "owner": "B", "content": "Hello world", "createdAt": 3610, "likes": 3 },
{ "owner": "C", "content": "Bark Bark", "createdAt": 3615, "likes": 1 },
{ "owner": "C", "content": "Bark Bark Bark", "createdAt": 3619, "likes": 0 },
{ "owner": "A", "content": "Second bark", "createdAt": 3, "likes": 20 },
{ "owner": "A", "content": "Third bark!", "createdAt": 6, "likes": 2 },
{ "owner": "C", "content": "Bark", "createdAt": 5, "likes": 1 },
{ "owner": "A", "content": "First bark!", "createdAt": 1, "likes": 1 }
```

#### Sort Order for Guests

Both `authed` and `guest` users will see Barks sorted by likability, but guests
will see _all_ Barks in the system regardless of who created them. Unless they
are not following anyone, `authed` users will only see Barks _from the users
they're directly following_.

If an `authed` user isn't following any other users yet, they should see the
same Barks a `guest` would. When there are no more Barks to show an `authed`
user (i.e. they scrolled to the absolute bottom), they should also begin seeing
the same Barks a `guest` would. **No user will ever see an empty Home view**
unless there is an error.

## Requirement 3

**Pack view: create and interact with Barks from the most important users.**

When an `authed` user [follows](#requirement-8) another user, they'll have the
choice to add that user to their _Pack_. This has the same effect as normally
following the user with one additional feature: Barks owned by users from the
Pack _also_ appear in this separate view. Like the [Home view](#requirement-2),
the Pack view shows the user a list of Barks sorted in order of most likeable,
but this view will filter out any Barks with owners not in the Pack.

`authed` users can also add and remove other users from their Pack without
leaving this view.

This is a private view unique to each user. `guest` users cannot access this
view.

## Requirement 4

**Bookmark view: view and manage Barks saved for later.**

This view shows an `authed` user the list of Barks they've bookmarked. Unlike
the other views that list Barks, **this view will sort Barks by bookmarked
time** in descending order. That is: the most recently bookmarked Barks will
appear first regardless of who owns them or when they were created. Further,
Barks in this view will have a second timestamp detailing when the Bark was
bookmarked.

Your app must record timing data (e.g. timestamp) when your user bookmarks a
Bark. Later, if this timing data is not available for whatever reason, this
should be reflected in the UI (e.g. a warning) and bookmarked Barks should be
organized by creation date instead.

`authed` users can also remove bookmarks without leaving this view.

This is a private view unique to each user. `guest` users cannot access this
view.

## Requirement 5

**Auth view: `authed` user registration and login.**

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
- The answer to a simple CAPTCHA challenge of some type <sup>\<required\></sup>
  - Example: `what is 2+2=?`
  - Teams must not call out to any API for this. Teams must create the CAPTCHA
    manually.

> Unlike past problem statements, user creation/deletion is managed for you
> through
> [the API](https://hscckhug3eb6.docs.apiary.io/#/reference/0/user-endpoints/users-post).
> However, dealing with login credentials and other custom user data is still
> your team's responsibility. Use of a local database is recommended.

`authed` users can use this view to logout, though there should be an easier way
to logout than returning to this view.

Additional constraints:

- Guests will be prevented from logging in for 1 hour after 3 failed login
  attempts. Guests will always see how many attempts they have left.
- Guests will have the option to use
  [remember me](https://www.troyhunt.com/how-to-build-and-how-not-to-build)
  functionality to maintain long-running authentication state. That is: if
  guests login and want to be "remembered," they will not be logged out until
  they manually log out.

## Requirement 6

**`authed` users can _mention_ other users in their Barks.**

`authed` users can "mention" other users by authoring a Bark containing an `@`
followed by the target user's username, e.g.
`This is the bark @username mentioned`. If the user doesn't exist, it's just
text. If the user does exist, the text becomes a _mention_, which becomes a link
allowing other users to follow the mentioned user.

Barks that mention the user will appear in some form at the top of whatever view
they appear in where they remain until dismissed by the user. It will be clearly
communicated by the UI that the Bark is highlighted because of a mention.
Mentions that have been dismissed will not reappear at the top of the view.

## Requirement 7

**If a customer does not remember their password, they can use email to recover
their account.**

If a customer has forgotten their login credentials, they will be able to
recover their account by clicking a link in the recovery email sent to their
address.

<blockquote>

The app must not actually send emails out onto the internet. The sending of
emails can be simulated however you want, including outputting the would-be
email to the console. The app will not use an external API or service for this.
For full points, ensure you document how recovery emails are simulated when
submitting your solution.

</blockquote>

## Requirement 8

**`authed` users can follow other users.**

`authed` users can easily "follow" and "unfollow" other `authed` users **without
switching views**. When a user follows another user, the followed user's Barks
appear in the follower user's [Home view](#requirement-2).

Users can also add other users to their [Pack](#requirement-3), which is a
special type of "follow".

## Requirement 9

**The app will suggest other `authed` users to follow.**

The app will suggest up to 5 other users to follow based on:

- The users who authored Barks the `authed` user liked
- The users followed by a user the `authed` user follows (i.e. an **"indirect
  follow"**)

For example, if:

- User A follows users B and C
- User B follows users E, F, G, H, M, N
- User C follows users D, E, I, J, K, and L
- User A liked 1 Bark from user E and 3 Barks from user I

Then the app would suggest user A follow these users in the following order:

1. I (3 likes + 1 indirect follows)
2. E (1 likes + 2 indirect follows)
3. D (0 likes + 1 indirect follows)
4. F (0 likes + 1 indirect follows)
5. G (0 likes + 1 indirect follows)

If the user is not following anyone and has not liked any Barks, or has not
liked/followed enough content to be recommended five users, they are shown five
random users instead.

## Requirement 10

**Counts displaying like/rebark/bark-back totals
[will update in the UI in real time](https://www.internetlivestats.com/one-second/#tweets-band);
new Barks and bark-backs will appear as they happen.**

Whenever a Bark's count data changes in the API, the new data will be reflected
in the UI
[**without the page refreshing**](https://www.encodedna.com/javascript/practice-ground/default.htm?pg=auto-refresh-div-using-javascript-and-ajax)
or the user doing anything extra (like pressing a refresh button). This type of
automatic updating is called _asynchronous_ or
"[ajaxian](<https://en.wikipedia.org/wiki/Ajax_(programming)>)" since it occurs
outside the usual
[_synchronous_ event flow](https://stackoverflow.com/a/32456239/1367414).

Similarly, whenever a new Bark or bark-back is created, it will appear in the UI
of every relevant user soon after. This will happen without forcing a page
refresh.

Typically, asynchronous features are implemented
[using frontend timers](https://developer.mozilla.org/en-US/docs/Learn/JavaScript/Asynchronous/Timeouts_and_intervals).

> Whenever a Bark's metadata changes (e.g.
> [`likes`](https://hscckhug3eb6.docs.apiary.io/#/data-structures/0/bark)
> increases by 1), that means fresh data can be pulled from the API (e.g. using
> the
> [`GET /:bark_id/likes`](https://hscckhug3eb6.docs.apiary.io/#/reference/0/bark-endpoints/barks-bark-id-likes-get)
> endpoint to see the latest likes).

## Requirement 11

**The app will be performant.**

[The amount of time the application takes to load and sort data, display information, and act on input is progressively penalized](https://www.websitebuilderexpert.com/building-websites/website-load-time-statistics).
That is: the teams with the fastest load times will earn the most points and the
teams with the slowest load times will earn zero points from this requirement.

Average (median) is used to calculate load times. Measurements will include
initial page load times and, depending on the other requirements, various other
frontend UI response times.

> [Tail latencies](https://robertovitillo.com/why-you-should-measure-tail-latencies)
> (e.g. on startup with a
> [cold cache](https://stackoverflow.com/questions/22756092/what-does-it-mean-by-cold-cache-and-warm-cache-concept))
> are ignored. Only averages are considered.

> [FOUC](https://petrey.co/2017/05/the-most-effective-way-to-avoid-the-fouc) may
> also be penalized.

> To maximize performance, consider
> [caching](https://www.sitepoint.com/cache-fetched-ajax-requests)
> ([see also](https://web.dev/cache-api-quick-guide)) the result of data
> processing operations and using
> [range queries](https://hscckhug3eb6.docs.apiary.io/#/introduction/pagination)
> to retrieve only the data your app hasn't yet processed.

## Requirement 12

**Results and lists of items displayed in the frontend UI will be
[paginated using infinite scrolling](https://codepen.io/wernight/details/YyvNoW)
where appropriate.**

[Pagination](https://www.smashingmagazine.com/2007/11/pagination-gallery-examples-and-good-practices)
is the strategy of showing a limited number of a large set of results and
providing a navigation element where users can switch to different "pages" of
that large set.
[Infinite scroll](https://www.javascripttutorial.net/javascript-dom/javascript-infinite-scroll)
is a specific implementation of the pagination strategy. Facebook and
Instagram's infinite scroll news feeds are good examples.

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

As for database security, any passwords present in the database must be
[hashed](https://auth0.com/blog/hashing-passwords-one-way-road-to-security)
(**not** encrypted). We recommend using a
[salted SHA-256 hash construction](https://auth0.com/blog/adding-salt-to-hashing-a-better-way-to-store-passwords)
or something similar. You don't need to do anything fancy.
[There](https://nakedsecurity.sophos.com/2013/11/20/serious-security-how-to-store-your-users-passwords-safely)
[are](https://cheatsheetseries.owasp.org/cheatsheets/Password_Storage_Cheat_Sheet.html)
[many](https://www.vaadata.com/blog/how-to-securely-store-passwords-in-database)
[tutorials](https://dev.to/kmistele/how-to-securely-hash-and-store-passwords-in-your-next-application-4e2f)
for how to safely store passwords and other credentials in a database.

> Passwords stored in your database in
> [cleartext](https://simple.wikipedia.org/wiki/Cleartext), hashed incorrectly,
> or re-encoded (e.g. with
> [base64](https://base64.guru/blog/base64-encryption-is-a-lie)) will earn your
> team zero points for this requirement.

## Requirement 14

**The app will
[fail gracefully](https://getbootstrap.com/docs/5.0/forms/validation/#server-side)
when exceptional conditions are encountered.**

This includes handling API errors during fetch, login errors, random exceptions,
[showing spinners](https://getbootstrap.com/docs/4.4/components/spinners) when
content needs to load, etc.

> Every so often,
> [the API will respond with an `HTTP 555` error](https://hscckhug3eb6.docs.apiary.io/#/introduction/rules-of-api-access)
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
