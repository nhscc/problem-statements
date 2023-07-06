# BDPA NHSCC 2023 Problem Statement (part 1)

> See also: [API documentation](https://hsccrkby0uo4.docs.apiary.io)

BDPA has partnered with Business API Solutions LLC to build out a business and
opportunity-oriented online social media platform they're calling _InBDPA_.
Yours is one of the teams in the bidding war for this lucrative contract and
Business API Solutions wants to see a demo ASAP!

<details><summary>Summary of requirements (15 total)</summary>

> _Caching and [ETL](https://en.wikipedia.org/wiki/Extract,_transform,_load)_
> (i.e. regularly extracting data from the API and then transforming, storing,
> and querying it on your own) are essential to success with this problem
> statement.

The app supports four user types: guests, inners, staff, and administrators; the
latter three are all _authenticated_ users while guests are _unauthenticated_.
Authentication occurs via the API. Users interact with content via each other's
profiles, which are akin to living resumes, and by filtering through a list of
available industry and volunteer opportunities. Each profile and opportunity is
reachable via a unique URL. While all users can view profiles, unauthenticated
users see only a small subset of user profile information. Similarly,
unauthenticated users cannot access any opportunities at all.

The app has at least five views: _Home_, _Profile_, _Opportunity_, _Admin_, and
_Auth_. The Home view is the landing page of your app. The Profile view renders,
depending on the URL, a specific user's profile. The Opportunity view allows an
authenticated user to view available opportunities, and allows staff users to
create, edit, and delete opportunities. The Admin view allows administrators to
create, manage, delete, and impersonate other users and view system-wide
statistics. The Auth view is used for handling user authentication.

Note that time-based data in the API is represented as the number of
milliseconds elapsed since January 1, 1970 00:00:00 UTC. See
[the API documentation](https://hsccrkby0uo4.docs.apiary.io/#/introduction/tips-for-debugging)
for details. Further note that you **must** use the API to complete this problem
statement, including interacting with data from other chapters, though you may
consider a hybrid approach where you have your own database storing non-API
data.

</details>

> We're looking for feedback! If you have any opinions or ideas, contact us on
> Slack.

🚧 🚧 To avoid disqualification, please take note of the following:

- Unlike PS2, PS1 is a "chapter-wide problem statement". That is: all students,
  coaches, and coordinators in the chapter can teach to, talk about, and
  collaborate on a solution. And then, when the conference comes around, your
  chapter sends your best five students to finish the job.
- Your solution’s landing page must be available at `http://127.0.0.1:3000`
  (localhost) on your team's AWS WorkSpace. **Judges must not have to type in
  anything other than `http://127.0.0.1:3000` to reach your app.**
- Your solution’s source code must be located at `%USERPROFILE%\Desktop\source`
  on your team's AWS WorkSpace. You can have other files located elsewhere, so
  long as they are also visible from the aforesaid directory.
- HTTP requests to the API must be sent with an `Authorization` header (`Key`
  header is deprecated). See the
  [API documentation](https://hsccrkby0uo4.docs.apiary.io) for details.

## Requirement 1

**Your app will support 4 types of users: guest, inner, staff, and
administrator.**

### Guests

- Are unauthenticated users (i.e. don't have to login)
- Can access the [Auth view](#requirement-6)
- Can access only a limited version of the [Profile view](#requirement-3)

### Inners (Authenticated)

- Are authenticated users (i.e. users that have already logged in)
- Can access the full [Profile view](#requirement-3)
- Can access only a limited version of the [Opportunity view](#requirement-4)

### Staff (Authenticated)

- Are authenticated users (i.e. users that have already logged in)
- Can access the full [Profile view](#requirement-3)
- Can access the full [Opportunity view](#requirement-4)

### Administrators (Authenticated)

- Are authenticated users (i.e. users that have already logged in)
- Can access the full [Profile view](#requirement-3)
- Can access the full [Opportunity view](#requirement-4)
- Can access the [Admin view](#requirement-5)
- Can impersonate (login as) other users
- Can promote `inner` accounts into `staff` accounts, and `staff` into
  `administrator` accounts

## Requirement 2

**Home view: display the InBDPA landing page featuring the hard sell.**

This is the "home page" of your service, and is the first page users will land
on when they navigate to your app. The purpose of this view is to sell the
viewer on the awesomeness of your application/solution while introducing them to
your service's features.

A good example would be [LinkedIn's landing page](https://linkedin.com).

## Requirement 3

**Profile view: display and/or modify an authenticated user's profile page.**

This view is responsible for rendering users' profile information. Each
authenticated user has a unique URL that leads directly to their profile page.

When viewed by a `guest`, a "limited" version of the view displays the following
in an aesthetically pleasing manner:

- The user's username
- The user's type
- The user's About section (if available)
- The number of [first and second -order connections](#requirement-9) the user
  has (e.g. "X connections")
  - Note that third-order connections should not be included in this count

When viewed by an authenticated user, the "full" view displays the following in
an aesthetically pleasing manner:

- Everything displayed in the "limited" view
- The number of times this user's profile has been viewed
- The number of active sessions currently viewing this profile
- The user's Experience section (if available)
- The user's Education section (if available)
- The user's Volunteering section (if available)
- The user's Skills section (if available)
- The user's status as a
  [first, second, or third -order connection](#requirement-9) relative to the
  authenticated viewer, or an appropriate indication if they are not connected

When an authenticated user views a profile to which they're
[connected](#requirement-9), they can also view that profile's first, second,
and third -order connections as a list of usernames (i.e. hyperlinks leading to
their respective profiles). However, only usernames of users _to which the
authenticated user is also connected_ are shown.

For instance: suppose the connection between five users goes **A➔B➔C➔D➔E** where
user `A` is connected to user `B`, `B` is connected to user `C`, and so on. Then
`A`, viewing user `D`'s profile, can see a list of `D`'s connections since `A`
and `D` are third-order connected. In that list, `A` will see a link to `C`'s
profile but they _will not_ see user `E` at all since `A` and `E` are not
connected. Regardless, `E`, as a first-order connection of `D`, will still be
counted in the "X connections" displayed on `D`'s profile, so `A` would still
see "4 connections".

> Ensuring your users' social graphs cannot be arbitrarily crawled by the public
> [is a huge privacy and security concern](https://www.theguardian.com/technology/2013/jan/23/facebook-graph-search-privacy-concerns).
> Your app must balance the need to keep user data private with the inherently
> leaky abstraction that is social media. This helps prevent the stalking and
> harassment of your users and is the reason `guest` users cannot see any
> details about any user connections.

When an authenticated user views their own profile, the full view additionally
allows the user to edit their profile's information. Specifically:

- They can change any of the information in any of their sections (About,
  Experience, Education, etc)
- They can change their public profile URL

Authenticated users can also add and remove other users as
[connections](#requirement-9) from this view. There is currently no permission
system requirement yet, so any user can connect to or disconnect from any other
user at any time.

### Profile URLs

Each authenticated user has a unique URL that leads directly to their public
profile page. Your team is free to decide the scheme of this URL.

For example: suppose a solution were using a potential URL scheme of
`{app-url}/in/{profile-name}`. If `{app-url}` were `https://my-app.com` and
`{profile-name}` were `9710435961`, then the profile would be reachable at
`https://my-app.com/in/9710435961`.

Using this view, authenticated users must be able to change the `{profile-name}`
component of their unique URL. **By default, the `{profile-name}` is a random
alphanumeric string.** A user changing their public profile URL must not change
their username or email.

> Public profile URLs are not stored at the API level and must be stored
> locally.

### Profile Pictures And Cover Photos

All users must have a profile picture. By default, this picture is provided by
[Gravatar](https://gravatar.com), an API service that associates people's email
addresses with profile pictures of their choosing. Users can also use this view
to upload their own cover photo.

#### Custom Cover Photo

A profile's cover photo (aka: background images, profile banners) must be among
[the first thing people see](https://buffer.com/library/ideal-cover-photo-size/)
when this view is loaded. It should be large, spanning most of the width of the
page, and appear at the top of the view with short height.

This view must allow users, _when viewing their own profile page_, to optionally
upload their own custom cover photo.

> This picture will not be stored in the API and must be stored locally by your
> solution.

#### Gravatar Profile Picture

To get a user's profile picture from their email,
[follow these instructions](https://en.gravatar.com/site/implement/images).

For example, one of the gravatars used by the creator of this problem statement
is:

<img src="https://www.gravatar.com/avatar/ff8fb2b91d470633184505d5e1f15366" /><br />

Represented by the following HTML:

```HTML
<img src="https://www.gravatar.com/avatar/ff8fb2b91d470633184505d5e1f15366" />
```

To get aesthetically pleasing images for users that whose email addresses are
not associated with a gravatar, just append the `?d=identicon` query string to
the end of all your gravatar URIs. Addresses _with_ gravatars will display like
normal, but those _without_ will get a neat hash-based random image.

For example, here's a gravatar using a fake hash:

<img src="https://www.gravatar.com/avatar/12345678912345678912345678900000?d=identicon" /><br />

Represented by the following HTML:

```HTML
<img src="https://www.gravatar.com/avatar/12345678912345678912345678900000?d=identicon" />
```

### Profile Sections

Users can optionally provide and display specifics about themselves (About
section), their industry experience (Experience section), their education
(Education section), their volunteer experience (Volunteering section), or their
skills (Skills section).

The About section is freeform. Users can provide up to 1,000 characters of text.

The Experience section lists up to 10 items each with a max 100 character title
(e.g. "Front End Lead Developer at Google"), start and end date, max 30
character location, and max 250 character description.

The Education section lists up to 10 items each with a max 100 character title
(e.g. "University of Chicago"), start and end date, max 30 character location,
and max 250 character description.

The Volunteering section lists up to 10 items each with a max 100 character
title (e.g. "BDPA Coach"), start and end date, max 30 character location, and
max 250 character description.

The Skills section lists up to 10 skills, each with a max string length of 30
characters (e.g. "JavaScript").

## Requirement 4

**Opportunity view: create, manage, and/or view job opportunities.**

This view is responsible for rendering opportunities, which are job offers and
volunteer positions, as an aesthetically pleasing listing. Each opportunity must
have its own unique URL.

When viewed by an `inner` user, a "limited" version of this view displays the
following in an aesthetically pleasing manner:

- Opportunities appear sorted in descending order of creation date (most recent
  opportunities appear first)
- Opportunities are displayed with their max 100 character title, number of
  views, number of active viewers (sessions)
- Clicking on an opportunity shows the max 3,000 character contents of the
  opportunity rendered as
  [Markdown](https://www.markdownguide.org/getting-started)
  - Your app will have to use a library to render the Markdown into HTML when
    users access the Profile view. Research and explore which Markdown library
    is best for your purposes.
    [Here are some suggestions to start with](https://npmcompare.com/compare/markdown,markdown-it,marked,remarkable,showdown)

`staff` users can additionally access the "full" version of this view, which
allows them to create new opportunities, edit opportunities they've created, and
delete opportunities they've created.

This view cannot be accessed at all by `guest`s. When a `guest` navigates to
this page, they should be asked to [login](#requirement-6).

## Requirement 5

**Admin view: view statistics and manage all authenticated users.**

This view allows `administrator`s to manage existing users and opportunities.

Specifically, `administrator`s can:

- Force a non-`administrator` user to log out immediately (i.e. as soon as their
  next HTTP response from the server)
- Impersonate a non-`administrator` user
  - When impersonating a user, your app will treat the `administrator` as if
    they had logged out and successfully logged back in as the user they wish to
    impersonate
  - When impersonating a user, a button/link must be added to the main
    navigation element that, which clicked, allows the `administrator` to return
    to the Admin view with their original `administrator` authentication
- Create new `administrator`, `staff`, or `inner` accounts
- Promote `inner` accounts into `staff` accounts, and `staff` into
  `administrator` accounts
- View generic statistics about the system including:
  - Total number of users in the system
  - Total number of opportunities in the system
  - Total number of views in the system
  - Total number of active viewers (sessions) in the system

## Requirement 6

**Auth view: register new users and authenticate existing users.**

`guest` users can use this view to authenticate (login) using their _username_
and their _password_. This sensitive information is referred to as a user's
_credentials_.

`guest` users can also use this view to register new credentials and create new
`inner` accounts.

### Authenticating Credentials (Login)

Your app must use
[the API](https://hsccrkby0uo4.docs.apiary.io/#/reference/0/user-endpoints/users-username-auth-get)
to authenticate the `guest` user _instead of_ retrieving the user's credentials
from a local database. Your app will do this by sending the API a
[digest value](https://developer.mozilla.org/en-US/docs/Glossary/Digest) derived
from the username and password provided. See
[the API documentation](https://hsccrkby0uo4.docs.apiary.io/#/reference/0/user-endpoints/users-username-auth-get)
for more details.

### Revoking Authentication (Logout)

If `authed`, the user can choose to _logout_, after which your app will treat
them like any other `guest` user. Logging a user out does not require a call to
the API.

### Registering New Credentials

There is an open registration feature `guest` users can use to register a new
account. When they do, they must provide at least the following where required:

- Desired username <sup>\<required\></sup>
  - Must be alphanumeric (dashes and underscores are also allowed).
- Email address <sup>\<required\></sup>
- Password <sup>\<required\></sup>
  - Password strength must be indicated as well. Weak passwords will be
    rejected. A weak password is ≤10 characters. A moderate password is 11-17
    characters. A strong password is above 17 characters.
- The answer to a simple CAPTCHA challenge of some type <sup>\<required\></sup>
  - Example: `what is 2+2=?`
  - Teams must not call out to any API for this. Teams must create the CAPTCHA
    manually.

Your app must use
[the API](https://hsccrkby0uo4.docs.apiary.io/#/reference/0/user-endpoints/users-post)
to create the new user _instead of_ storing the user's information locally.

### Additional Constraints

- All new users start off as `inner`s. Only `administrator`s can promote `inner`
  accounts into `staff` accounts, or `staff` accounts into `administrator`
  accounts.
- Usernames and email addresses must be unique. That is: no two users can have
  the same username or email address. This is enforced at the API level.
- `guest` users will be prevented from logging in for 1 hour after 3 failed
  login attempts. `guest` users will always see how many attempts they have
  left.
- `guest` users will have the option to use
  [remember me](https://www.troyhunt.com/how-to-build-and-how-not-to-build)
  functionality to maintain long-running authentication state. That is: if a
  `guest` logs in and wants to be "remembered," they will not be logged out
  until they manually log out.
- You must use the API to create new users, authenticate and store their
  credentials, and handle all user data (where applicable). See
  [the API documentation](https://hsccrkby0uo4.docs.apiary.io/#/data-structures/0/user)
  for details on what the API will store for you. Any data not storable at the
  API level can be stored locally by your solution.

## Requirement 7

**If a user does not remember their password, they can use email to recover
their account.**

If a user has forgotten their login credentials, they will be able to recover
their account by clicking a link in the recovery email sent to their address.
Your app will then allow them to set a new password.

<blockquote>

The app must not actually send emails out onto the internet. The sending of
emails can be simulated however you want, including outputting the would-be
email to the console. The app will not use an external API or service for this.
For full points, ensure you document how recovery emails are simulated when
submitting your solution.

</blockquote>

## Requirement 8

**A navigation element containing the BDPA logo, your app title, and a subset of
user data is permanently visible to users.**

In every view, a
[navigation element](https://blog.hubspot.com/website/main-website-navigation-ht)
is permanently visible containing the BDPA logo (downloadable
[here](https://bdpa.org/wp-content/uploads/2020/12/f0e60ae421144f918f032f455a2ac57a.png))
and the title of your app.

## Requirement 9

**Users' second-order and third-order connections must be tracked by your
solution.**

For example, suppose `user-A` is connected to `user-B`, and `user-B` is
connected to `user-C`, then:

- `user-A` and `user-B` are considered _first-order connections_ in the system.
  - One hop: A➔B
- `user-B` and `user-C` are considered first-order connections in the system.
  - One hop: B➔C
- `user-A` and `user-C` are considered _second-order connections_ in the system.
  - Two hops: A➔B➔C

If `user-A` is also connected to `user-D`, then:

- `user-D` and `user-C` are considered _third-order connections_
  - Three hops: D➔A➔B➔C

If `user-A` were the authenticated user viewing other users via the
[Profile view](#requirement-3), then:

- When viewing `user-B`'s profile, `user-A` would see that `user-B` is one of
  their first-order connections
- When viewing `user-C`'s profile, `user-A` would see that `user-C` is one of
  their second-order connections
- When viewing `user-D`'s profile, `user-A` would see that `user-D` is one of
  their first-order connections

Further suppose `user-E` is connected to `user-D`. Then:

- When viewing `user-E`'s profile, `user-A` would see that `user-E` is one of
  their second-order connections
- The system does not consider `user-E` and `user-C` as "connected" since
  fourth-order connections and beyond are ignored

Note that the API **only stores first-order connections**. You will have to
calculate users' second- and third- order connections.

> Hint: to keep things speedy and avoid getting rate limited, keep a
> [denormalized](https://www.geeksforgeeks.org/denormalization-in-databases)
> local cache of users' connections. Do this instead of constantly hitting the
> API O(n<sup>3</sup>) times just to calculate and recalculate a user's second-
> and third- order connections, which would likely
> [severely negatively impact your app's performance](#requirement-11). Instead,
> check every now and then for updates to users' connections by leveraging
> `updatedAfter` when polling
> [the `/users` endpoint](https://hsccrkby0uo4.docs.apiary.io/#/reference/0/user-endpoints/users-get),
> and structure your cache such that finding, updating, and deleting connections
> is efficient enough.

## Requirement 10

**Every view (Home, Profile, Opportunity, Admin, Auth) must use the API to
asynchronously update the total number of views (where applicable) and number of
active sessions.**

When the [Profile view](#requirement-3) loads a specific user's profile, or the
[Opportunity view](#requirement-4) loads a particular opportunity, or the
[Home](#requirement-2), [Admin](#requirement-5), or [Auth](#requirement-6) views
are loaded, your app must do two things:

1. If viewing a specific profile or opportunity: increment the (monotonic) total
   number of views associated with that profile or opportunity
2. [Add or renew an "active session" entry](https://hsccrkby0uo4.docs.apiary.io/#/reference/0/session-endpoints/sessions-session-id-patch)
   associated with the client

Active sessions represent users (`guest` or otherwise) that are currently
interacting with one of these views. When registering a new active session,
you'll receive a
[`session_id`](https://hsccrkby0uo4.docs.apiary.io/#/reference/0/session-endpoints/sessions-post)
that you can use to
[renew](https://hsccrkby0uo4.docs.apiary.io/#/reference/0/session-endpoints/sessions-session-id-patch)
that session every so often or
[manually expire (delete)](https://hsccrkby0uo4.docs.apiary.io/#/reference/0/session-endpoints/sessions-session-id-delete)
it.

Clients should regularly renew active sessions for as long as the user is
interacting with one of these views. Once the client navigates away from the
view, the session should be
[manually expired](https://hsccrkby0uo4.docs.apiary.io/#/reference/0/session-endpoints/sessions-session-id-delete)
and no longer renewed. That is: shortly after the user leaves a view and, hence,
stops being an "active" viewer of a view, your solution must attempt to
[manually expire](https://hsccrkby0uo4.docs.apiary.io/#/reference/0/session-endpoints/sessions-session-id-delete)
the active session associated with said view. There are
[several](https://dev.to/amersikira/top-3-ways-to-easily-detect-when-a-user-leaves-a-page-using-javascript-2ce4)
[methods](https://developer.mozilla.org/en-US/docs/Web/API/Beacon_API) for
determining when a user is trying to navigate away from a web page.

Additionally, when displaying total views and/or total active sessions for a
profile/opportunity or in the [Admin view](#requirement-5), the displayed values
must refresh asynchronously.

<blockquote>

This type of automatic updating/revalidating of data is called _asynchronous_ or
"[ajaxian](<https://en.wikipedia.org/wiki/Ajax_(programming)>)" since it occurs
outside the usual
[_synchronous_ event flow](https://dev.to/lydiahallie/javascript-visualized-event-loop-3dif).
There are
[many](https://www.encodedna.com/javascript/practice-ground/default.htm?pg=auto-refresh-div-using-javascript-and-ajax)
[solutions](https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API/Using_Fetch#uploading_json_data),
including
[interval revalidation](https://swr.vercel.app/docs/revalidation#revalidate-on-interval),
[focus revalidation](https://swr.vercel.app/docs/revalidation#revalidate-on-focus),
and
[visibility-based revalidation](https://stackoverflow.com/questions/8661051/update-only-visible-dom-elements)
(i.e. updating data only for elements that are currently visible). Another
solution is to use
[frontend timers](https://developer.mozilla.org/en-US/docs/Learn/JavaScript/Asynchronous/Timeouts_and_intervals)
to regularly check the API for new data every now and then.

</blockquote>

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

> [FOUC](https://stackoverflow.com/a/43823506/1367414) may also be penalized.

> To maximize performance, consider
> [caching](https://www.sitepoint.com/cache-fetched-ajax-requests)
> ([see also](https://web.dev/cache-api-quick-guide)) the result of data
> processing operations and using
> [range queries](https://hsccrkby0uo4.docs.apiary.io/#/introduction/pagination)
> to retrieve only the data your app hasn't yet processed.

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
> [the API will respond with an `HTTP 555` error](https://hsccrkby0uo4.docs.apiary.io/#/introduction/rules-of-api-access)
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
