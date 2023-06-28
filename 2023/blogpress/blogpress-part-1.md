# BDPA NHSCC 2023 Sample Problem Statement (part 1)

> See also: [API documentation](https://hsccjcat4d54.docs.apiary.io)

PressWord Software believes they can do blogging better. They've hired your
freelance team to build out the _BlogPress_ platform and prove it.

<details><summary>Summary of requirements (15 total)</summary>

You are essentially building
[headless CMS](https://www.contentful.com/headless-cms) software
[as a service](https://en.wikipedia.org/wiki/Software_as_a_service), which hosts
bloggers and their blogs.

The app supports three user types: guests, bloggers, and administrators.
Authentication occurs via the API. Users interact with content via each
blogger's unique blog, which is a sort of self-contained miniature website that
can be modified and extended by the blogger. Each user's blog is reachable via a
unique URL. While all users can view blogs and bloggers can only manage their
own unique blogs, administrators can view and manage any blog in the system.

The app has at least five views: _Home_, _Blog_, _Builder_, _Admin_, and _Auth_.
The Home view is the landing page of your app. The Blog view renders, depending
on the URL, a specific page from a specific user's blog. The Builder view allows
an authenticated user to manage their specific blog. The Admin view allows
administrators to manage any blog in the system as well as manage other users
and create new administrators. The Auth view is used for handling user
authentication.

Note that time-based data in the API is represented as the number of
milliseconds elapsed since January 1, 1970 00:00:00 UTC. See
[the API documentation](https://hsccjcat4d54.docs.apiary.io/#/introduction/tips-for-debugging)
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
  [API documentation](https://hsccjcat4d54.docs.apiary.io) for details.

## Requirement 1

**Your app will support 3 types of users: `guest`, `blogger`, and
`administrator`.**

### Guests

- Are unauthenticated users (i.e. don't have to login)
- Can access the [Auth view](#requirement-6)
- Can access the [Blog view](#requirement-3)

### Bloggers

- Are authenticated users (i.e. users that have already logged in)
- Can access their specific [Builder view](#requirement-4)
- Can access all the views `guest`s can access

### Administrators

- Are authenticated users (i.e. users that have already logged in)
- Can access the [Admin view](#requirement-5)
- Can access every other view
- Do _not_ have a blog associated with their account

## Requirement 2

**Home view: display the BlogPress landing page featuring the hard sell.**

This is the "home page" of your service, and is the first page users will land
on when they navigate to your app. The purpose of this view is to sell the
viewer on the awesomeness of your application/solution while introducing them to
your service's features.

A good example would be [WordPress's landing page](https://wordpress.com/).

## Requirement 3

**Blog (Head) view: display a user's blog.**

This view is responsible for rendering a specific `blogger`'s blog (specifically
its [pages](#creating-and-editing-a-page)). From this view, users can view any
of the blog's [pages rendered as Markdown](#creating-and-editing-a-page).

This view must be accessible by any user (including `guest`s) via unique URL.
That is: each user's blog must have a unique URL that leads to that blog. For
example, if your solution is located at http://localhost:3000, the
[root page](#the-root-page) of a blog named "cool-blogio" might be found at
http://localhost:3000/blogs/cool-blogio, assuming a potential URL scheme of
`{app-url}/blogs/{blog-name}`. A [page](#creating-and-editing-a-page) named
"about-us" from the same blog might be found at
http://localhost:3000/blogs/cool-blogio/about-us.

Additionally, this view will always display a
[blog-specific standard navigation element](#requirement-8).

## Requirement 4

**Builder (Body) view: manage a user's blog.**

This view allows authenticated users to manage their specific blog.

From this view, authenticated users can:

- Create new blog pages
- Delete existing blog pages (except the "home" page)
- Edit the [Markdown contents](#creating-and-editing-a-page) of existing pages
- View a per-page breakdown of (1) the total number of views and (2) the total
  number of [active (non-expired) viewers](#requirement-10)
- View the cumulative total number of views for the entire blog (sum of all page
  views)
- Select which pages will be listed in the blog's
  [navigation element](#navigation-element)

Every blog, when it is first created along with the user account, will have a
default "home" page created for them by the API. This page will be considered
their blog's [root page](#the-root-page). This default home page will also be
added to the new blog's [navigation element](#navigation-element) by default,
which the `blogger` can change later.

### Creating And Editing A Page

When authoring a page, the user must be able to see a preview before they submit
it ([example](https://markdown-it.github.io/)). Since pages are authored in
[Markdown](https://www.markdownguide.org/getting-started), your app will have to
use a library to render the Markdown into HTML, which you can then display to
your users. Research and explore which Markdown library is best for your
purposes.
[Here are some suggestions to start with](https://npmcompare.com/compare/markdown,markdown-it,marked,remarkable,showdown).

### The Root Page

Each blog must have a "root" (aka _home_ or _landing_) page. It is a page just
like any other page, except its name does not need to be entered as part of the
blog's URL to visit it and it cannot be deleted by the user. For example, if
your solution is located at http://localhost:3000, the root page of a blog named
"cool-blogio" would be rendered at http://localhost:3000/blogs/cool-blogio,
assuming a URL scheme of `{app-url}/blogs/{blog-name}`.

Given these constraints, blog names must be unique and page names must be unique
per `blogger` account. This is enforced at the API level.

### Navigation Element

Each blog has its own
[navigation element](https://blog.hubspot.com/website/main-website-navigation-ht),
which is just a list of _at most_ five pages/URIs that will always appear at the
top of the [Blog view](#requirement-3).

See [requirement 8](#requirement-8) for more details.

## Requirement 5

**Admin view: view statistics and manage any user blog or account.**

This view allows `administrator`s to manage existing blogs and accounts.

Specifically, `administrator`s can:

- Create, edit, and delete any blog's pages
- Ban `blogger` users (prevents them from being able to login)
- Unban `blogger` users
- Create new `administrator` accounts
- View generic statistics about the system including:
  - Total number of users
  - Total number of blogs
  - Total number of pages
- View a list of users, the names of their blogs, and related statistics
  including:
  - If the user is banned or not
  - Cumulative total number of views for the entire blog (sum of all page views)
  - Total number of views per page (this data can be hidden behind a drop-down
    or some other "focus" mechanic if desired)

Additionally, the list of users and associated statistics can be sorted in the
UI by:

- Username / email address
- Banned status
- Total number of views across the entire blog

> Reminder: [consider caching](#requirement-11) where appropriate.

## Requirement 6

**Auth view: register new users and authenticate existing users.**

`guest` users can use this view to authenticate (login) using an existing
_email_ and _password_. This sensitive information is referred to as a user's
_credentials_.

`guest` users can also use this view to register new credentials and create new
`blogger` accounts.

### Authenticating Credentials (Login)

Your app must use
[the API](https://hsccjcat4d54.docs.apiary.io/#/reference/0/user-endpoints/users-username-or-email-auth-post)
to authenticate the `guest` user _instead of_ retrieving the user's credentials
from a local database. Your app will do this by sending the API a
[digest value](https://developer.mozilla.org/en-US/docs/Glossary/Digest) derived
from the username, email address, and password (username + email + password)
provided. See
[the API documentation](https://hsccjcat4d54.docs.apiary.io/#/reference/0/user-endpoints/users-username-or-email-auth-post)
for more details.

### Revoking Authentication (Logout)

If `authed`, the user can choose to _logout_, after which your app will treat
them like any other `guest` user. Logging a user out does not require a call to
the API.

### Registering New Credentials

There is an open registration feature `guest` users can use to register a new
account. When they do, they must provide at least the following where required:

- Desired username <sup>\<required\></sup>
  - Must be lowercase alphanumeric (dashes and underscores are also allowed).
- Email address <sup>\<required\></sup>
- Password <sup>\<required\></sup>
  - Password strength must be indicated as well. Weak passwords will be
    rejected. A weak password is ≤10 characters. A moderate password is 11-17
    characters. A strong password is above 17 characters.
- [Blog name](#requirement-4) <sup>\<required for non-administrators\></sup>
- The answer to a simple CAPTCHA challenge of some type <sup>\<required\></sup>
  - Example: `what is 2+2=?`
  - Teams must not call out to any API for this. Teams must create the CAPTCHA
    manually.

Your app must use
[the API](https://hsccjcat4d54.docs.apiary.io/#/reference/0/user-endpoints/users-post)
to create the new user _instead of_ storing the user's information locally.

### Additional Constraints

- Usernames, email addresses, and blog names must be unique. That is: no two
  users can have the same username, email address, or blog name. This is
  enforced at the API level.
- `guest` users will be prevented from logging in for 1 hour after 3 failed
  login attempts. `guest` users will always see how many attempts they have
  left.
- `guest` users will have the option to use
  [remember me](https://www.troyhunt.com/how-to-build-and-how-not-to-build)
  functionality to maintain long-running authentication state. That is: if a
  `guest` logs in and wants to be "remembered," they will not be logged out
  until they manually log out.
- You must use the API to create new users, authenticate and store their
  credentials, and track any relevant metadata. See
  [the API documentation](https://hsccjcat4d54.docs.apiary.io/#/data-structures/0/user)
  for details on what the API will store for you. Anything not storable at the
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

**A navigation element containing the BDPA logo, the blog name, the blog's
[navigation links](#requirement-4), and a subset of user data is permanently
visible to users when rendering the [Blog view](#requirement-3).**

In the [Blog view](#requirement-3), a
[navigation element](https://blog.hubspot.com/website/main-website-navigation-ht)
is permanently visible containing:

- The BDPA logo (downloadable
  [here](https://bdpa.org/wp-content/uploads/2020/12/f0e60ae421144f918f032f455a2ac57a.png))
- The [name of the blog currently being rendered](#requirement-4)
- [Said blog's navigation links](#navigation-element)
- If the current user is authenticated: the username/email of the current user
  and a logout link
- If the current user is unauthenticated: [login and register](#requirement-6)
  links
- If the user is an `administrator`: a link to the [Admin view](#requirement-5)
- If the user is a `blogger` (or `administrator`): a link to the
  [Builder view](#requirement-4) for the currently rendered blog

> Hint: keep maximum interoperability with other teams’ solutions in mind when
> deciding on your app’s URL scheme _and_ how you’ll handle navigation link
> `href`s and HTTP 404 errors.

## Requirement 9

**User-specific [Blog](#requirement-3) and [Builder](#requirement-4) views must
be reachable from
[RESTful URLs](https://stackoverflow.com/a/11437729/1367414).**

For example, given a potential URL scheme of
`{app-url}/({blog-name}|auth|builder)/{?blog-page}`, a user with a blog
[named](#requirement-4) "cool-blogio" could have their
[Blog view](#requirement-3) reachable at http://localhost:3000/cool-blogio
([root page](#the-root-page)) and their [Builder view](#requirement-4) at
http://localhost:3000/builder/cool-blogio. If their blog has "home"
([root page](#the-root-page)), "about," and "contact"
[pages](#creating-and-editing-a-page), those would be reachable at
http://localhost:3000/cool-blogio/home, http://localhost:3000/cool-blogio/about,
and http://localhost:3000/cool-blogio/contact respectively. Further, the Auth
view might be available at http://localhost:3000/auth and the Home view at
http://localhost:3000.

With this potential URL scheme, no user can create a blog named "auth" or
"builder".

> You can come up with any RESTful URL scheme you want. You do not have to use
> the example scheme described above. Though remember that the input limits you
> place on your solution's frontend (like not allowing certain blog names) might
> not exist in other teams' solutions.

## Requirement 10

**[Blog view](#requirement-3) must asynchronously update number of views and
number of active viewers (sessions). Builder and Admin views must asynchronously
report this information.**

When the [Blog view](#requirement-3) loads a specific blog's page, your app must
do two things:

1. Increment the (monotonic) total number of views associated with that blog's
   page
2. [Add or renew an "active session" entry](https://hsccjcat4d54.docs.apiary.io/#/reference/0/blog-endpoints/blogs-blog-name-pages-page-name-sessions-post)
   associated with that user

Active sessions represent users (`guest` or otherwise) that are currently
interacting with a blog's page. When registering a new active session, you'll
receive a
[`session_id`](https://hsccjcat4d54.docs.apiary.io/#/reference/0/blog-endpoints/blogs-blog-name-pages-page-name-sessions-post)
that you can use to
[renew](https://hsccjcat4d54.docs.apiary.io/#/reference/0/blog-endpoints/blogs-blog-name-pages-page-name-sessions-post)
that session every so often or
[manually expire (delete)](https://hsccjcat4d54.docs.apiary.io/#/reference/0/blog-endpoints/blogs-blog-name-pages-page-name-sessions-post)
it.

Clients should regularly renew active sessions for as long as the user is
viewing a blog's page. Once the client navigates away from the blog page, the
session should no longer be renewed.

As for the [Admin](#requirement-5) and [Builder](#requirement-4) views, they
must display statistics about the total number of views and total number of
active (non-expired) users of each of the current blog's pages. The updated
totals must eventually appear within these two views _without the page
refreshing_ or the user doing anything extra, like pressing a refresh button.

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
> [range queries](https://hsccjcat4d54.docs.apiary.io/#/introduction/pagination)
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
> [the API will respond with an `HTTP 555` error](https://hsccjcat4d54.docs.apiary.io/#/introduction/rules-of-api-access)
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
