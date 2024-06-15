# BDPA NHSCC 2024 Problem Statement (part 1)

> See also: [API documentation](https://hscc35a947d8.docs.apiary.io)

BDPA Elections, Inc. has been contracted to once again build yet another secure
electronic election system for democracy. The system will allow certain entities
to manage IRV- and CPL-based elections at scale without requiring voters to
appear at a physical voting location.

This is the solemn duty for which your dev team was assembled.

<details><summary>Summary of requirements (15 total)</summary>

The app supports four _authenticated_ user types: voters, moderators,
administrators, and reporters. Users can only be one type. Voters are the most
common type of user. They vote in elections and can view a complete listing of
past election results. Moderators manage elections to determine which voters are
eligible to vote in which elections. Administrators can create and manage other
users, create and manage elections, and determine which voters/moderators can
access which elections. Reporters can view the history of past election results
and nothing else.

Note that time-based data in the API is represented as the number of
milliseconds elapsed since January 1, 1970 00:00:00 UTC. See
[the API documentation](https://hscc35a947d8.docs.apiary.io/#/introduction/tips-for-debugging)
for details. Further note that you **must** use the API to complete this problem
statement, including interacting with data from other chapters, though you may
consider a hybrid approach where you have your own database storing non-API
data.

</details>

> We're looking for feedback! If you have any opinions or ideas, contact us on
> Slack.

🚧 🚧 To avoid disqualification, please take note of the following:

<details><summary>Expand</summary>

- **Any evidence of illegal remote access to AWS WorkSpaces or any other AWS
  resource will result in a steep penalty or immediate disqualification.**

- Unlike PS2, PS1 is a "chapter-wide problem statement". That is: all students,
  coaches, and coordinators in the chapter can teach to, talk about, and
  collaborate on a solution. And then, when the conference comes around, your
  chapter sends your best five students to finish the job.

- Your solution’s source code must be located at `%USERPROFILE%\Desktop\source`
  on your team's AWS WorkSpace. You can _also_ have other files located
  elsewhere so long as they are reachable (e.g.
  [junctioned/soft-linked](https://superuser.com/questions/343074/directory-junction-vs-directory-symbolic-link))
  from `%USERPROFILE%\Desktop\source`. **This is the only location judges are
  required to access when scoring your source code.**

- **Judges must not have to type in anything other than `http://127.0.0.1:3000`
  into the browser to reach your app.**

</details>

Additionally, observe the following guidelines so your solution doesn't
malfunction in front of everyone after being deployed to
`https://XYZ.submissions.hscc.bdpa.org`:

<details><summary>Expand</summary>

- Your solution’s landing page must be available at `http://127.0.0.1:3000`
  (localhost) on your team's AWS WorkSpace.

- Prefer relative URIs where possible _instead of_ hardcoding your app to use
  something like `127.0.0.1:3000`.

- Avoid hardcoding the protocol (e.g. use `//localhost:3000/some-file.png`
  instead of `http://localhost:3000/some-file.png`).

- **Avoid loading resources in the browser using ports other than 3000** (e.g.
  running two separate web-visible servers, one on 3000 and one on 80).

- If using websockets, stick to port 3000 and ensure your web server can handle
  [Upgrade requests](https://developer.mozilla.org/en-US/docs/Web/HTTP/Protocol_upgrade_mechanism)
  for your websocket-specific routes (e.g. `localhost:3000/ws`).

- Consider deploying your solution in production mode _instead of_ development
  mode, which is
  [slower and results in a degraded UX](https://stackoverflow.com/a/69080984/1367414)
  (e.g. showing errors that would normally be hidden in production, **costing
  you points**).

</details>

## Requirement 1

**Your app will support 4 types of users: voter, moderator, administrator, and
reporter.**

Users can only have a single type at a time. You are free to create other types.

Additionally, all users must be [authenticated](#requirement-6) before being
allowed to interact with the system. "Guest" users are not allowed except to
register a new account.

### Voters

- Are authenticated users (i.e. users that have already logged in)
- Can access the full [Election view](#requirement-3) and can vote in elections
  to which they have been assigned
- Can access their own personalized [Dashboard view](#requirement-4)
- Can access the [History view](#requirement-5)

### Moderators

- Are authenticated users (i.e. users that have already logged in)
- Can access their own personalized [Dashboard view](#requirement-4)
- Can assign `voters` to elections they moderate
- Can assign `reporters` to elections they moderate
- Can access the [History view](#requirement-5)

### Administrators

- Are authenticated users (i.e. users that have already logged in)
- Can access a limited version of the [Election view](#requirement-3) but cannot
  vote
- Can access their own personalized [Dashboard view](#requirement-4)
- Can assign `voters` to elections
- Can assign `reporters` to elections
- Can access the [History view](#requirement-5)
- Can access any other relevant view

#### The super administrator user

Note the special **super** user. The super user is a special `administrator`
that can create other `administrator` users. The super user must always exist,
even when your solution is reset to its initial state. There can only exist one
such user.

### Reporters

- Are authenticated users (i.e. users that have already logged in)
- Can view a paginated listing of all past (closed) elections in the system to
  which they have been assigned

## Requirement 2

**Your app will support elections.**

Each election in the system has at least the following components:

- A globally unique identifier automatically generated by the API
- A title
- A description
- The voting method used
- A unix epoch timestamp indicating when the election was created in the system
- A unix epoch timestamp indicating when the election opens
- A unix epoch timestamp indicating when the election closes
- A boolean representing if the election was deleted or not
- An array of options voters must rank from most favored to least favored
  ```javascript
  // Example
  ['jelly', 'butter', 'peanut butter'];
  ```
- An array of objects that maps voters to their rankings
  ```javascript
  // Example
  // See also: https://hscc35a947d8.docs.apiary.io/#/data-structures/0/ballot
  [
    {
      voter_id: 'someThey425',
      ranking: { 'peanut butter': 1, jelly: 2, butter: 3 }
    },
    {
      voter_id: 'someThem3312',
      ranking: { butter: 1, jelly: 2, 'peanut butter': 3 }
    }
  ];
  ```

> Warning: all of the above information **must** be stored using the API. You
> can cache it locally, but it **must** also be put into the API. Only accessing
> your local database without using the API will disqualify your solution.

Feel free to track any other information you deem necessary, but keep in mind
extra information cannot be stored using the API.

### Owned Elections

Elections that are added to the API by your team are considered "owned" by your
team. Necessarily, other elections in the system are considered "unowned" by
your team. All unowned elections are considered read-only at the API level.
Since your users cannot otherwise interact with these elections, they only need
to be shown in the [History view](#requirement-5) and anywhere else explicitly
noted.

### Election Immutability

Once an election is closed, its results become immutable, which means: none of
the information about that election can be modified by `administrators` and the
election itself cannot be deleted by `administrators`.

The [super user](#the-super-administrator-user) is exempt from these
restrictions; they can delete closed elections or modify some parts of them;
specifically: _title_, _description_, _opening time_, and _closure timestamp_.
Updating the closure timestamp to a time in the future will effectively re-open
a closed election, making it no different than any other open election.

Therefore, unless re-opened, the outcomes of closed elections can never be
modified. This also implies that if a user voted in an election before their
account was [deleted](#administrators) or [unassigned](#moderators), their
ballot must still count and the outcome of the election cannot change.

When using the API to update owned elections based on the actions of moderators
and administrators, you must decide what edge cases like changing an election's
_available options_, _opening time_, and _closing time_ means after an election
has already opened, already closed, and/or has already accepted votes.

## Requirement 3

**Election view: view and participate in an election.**

This view allows a `voter` to access and participate in an
[election](#requirement-2). A `voter` cannot use this view to access an election
they have not been assigned to. This view can also be used by `administrators`
to view the current state of an election, though they are not able to cast
votes.

For elections that have not yet opened, users should see when the election will
open.

Similar to the [History view](#requirement-5), when viewing election results
that are closed:

- The winning option will be emphasized in the frontend UI
- All eliminated options will be clearly marked
- The UI will indicate by how many votes the winning option won versus the total
  number of votes cast
- If the current user voted in said election, their first choice in the election
  will be most prominently marked

> Warning: whenever you're displaying the results of an election, **you must
> protect the identities of your voters**! Do not publicly reveal who voted for
> which options in the UI.

Additionally, `voters` have five minutes, or until the election closes
(whichever is sooner), to change/remove their vote after submitting it. After
five minutes, or once the election closes, `voters` can no longer change their
minds.

## Requirement 4

**Dashboard view: view and interact with a personalized user dashboard.**

This is the "home page" of your service for users that are authenticated, and is
the view to which newly authenticated users are redirected. Each user has their
own individualized dashboard.

When accessed by any user, this view will show:

- The name of the user, like "Ray" or "Ray Tiles"
- The IP address of the client recorded the previous five time this user logged
  in
  - That is: users will be able to see the last five times their account was
    logged into and from which IP
- The timestamp recorded the previous time this user logged in

You are free to display any other relevant information.

Users can use their dashboard to edit their own personal information, including
their name, email, password, and other metadata without going through an
`administrator`.

The very first time a newly-created user logs in, whereupon they are redirected
to this view, said user will be forced to change their password before being
allowed to interact with the system. **This only applies to accounts created by
an `administrator`** and not accounts registered by the user themselves via the
[Auth view](#requirement-6).

> Note that deleted elections should never be displayed to users that are not
> `administrators`. In such cases, deleted elections should be treated as if
> they don't exist at all.

Additionally:

**When accessed by a `voter`, this view will show:**

- _Open_ elections the user can currently participate in
- _Closed_ elections they participated in
- _Upcoming_ elections they will eventually be eligible to participate in

When displaying non-closed elections, they will be sorted in ascending order by
their opening time (i.e. elections that opened/will open earlier in time are
shown first). When displaying past elections, they will be sorted in descending
order by their closing time (i.e. elections that have closed later in time are
shown first).

**When accessed by a `moderator`, this view will:**

- Enable adding/removing one or more `voters` to/from elections they've been
  assigned to oversee
  - `administrators` assign `moderators` to oversee elections
- Enable adding/removing one or more `reporters` to/from elections they've been
  assigned to oversee

**When accessed by an `administrator`, this view will:**

- Enable adding/removing one or more `voters` to/from any election
- Enable assigning/removing one or more `moderators` to/from any election
- Enable assigning/removing one or more `reporters` to/from any election
- Allow creating new `voters`, `moderators`, and `reporters`
  - If the administrator is also the
    [super user](#the-super-administrator-user), they can create new
    `administrators` as well
- Allow viewing existing users and updating/deleting non-`administrator` users
  - `administrators` can update the personal data of any other user
  - `administrators` can view deleted users as well as all others
  - If the administrator is also the
    [super user](#the-super-administrator-user), they can delete other
    `administrators` but cannot delete themselves
- Allow viewing, creating, updating, and deleting elections in the system
  - Elections that are not [owned by your team](#requirement-2) will always be
    read-only at the API level. It should be clearly communicated via the UI
    that unowned elections cannot be updated/deleted by the `administrator`,
    only viewed
  - [Elections become immutable once they close](#requirement-2), after which
    they can no longer be updated unless they are re-opened
  - The administrator must choose among the available voting methods. The
    selected method will be used to determine the winner of the election.
    - Once an election has been created, the selected voting method cannot be
      changed.

**When accessed by a `reporter`, this view will show:**

- The most recent _closed_ elections in the system that the `reporter` has been
  assigned to ([if they are owned by your team](#requirement-2)). `reporters`
  can see all elections in the system that are not owned by your team regardless
  of assignment
  - It must be clear which elections are owned by your team and which are not.
- A link to the [History view](#requirement-5) in case the user wants to see
  more elections.

## Requirement 5

**History view: view the results of all past (closed) elections.**

This view makes available a paginated listing of all past (closed)
[election](#requirement-2) results in the entire system that the current user is
allowed to view. Past elections that are not
[owned by your team](#owned-elections) can always be viewed by anyone. However,
deleted elections can never be viewed and should not show up in the listing
_unless the user accessing this view is an `administrator`_.

Similar to the [Election view](#requirement-3), when viewing election results:

- The winning option will be emphasized in the frontend UI
- All eliminated options will be clearly marked
- The UI will indicate by how many votes the winning option won versus the total
  number of votes cast
- If the current user voted in said election, their choice in the election will
  be most prominently marked

When displaying past elections, they will be sorted in descending order by their
closing time (i.e. elections that have closed later in time are shown first)
initially. Users can choose to sort results by at least the following: title,
creation time, opening time, closing time.

> To satisfy this requirement, consider a proper
> [caching strategy](#requirement-11) to reduce load on the API and improve your
> app's performance.

## Requirement 6

**Auth view: authenticate existing users and register new users.**

Users can use this view to authenticate (login) using their _username_ and their
_password_. This sensitive information is referred to as a user's _credentials_.

No part of the system other than this view will be accessible to unauthenticated
users.

### Authenticating Credentials (Login)

Your app must bring its own authentication system that allows users to login and
logout at will. Users that are not logged in cannot interact with the system
outside of this view.

### Revoking Authentication (Logout)

Authenticated users can choose to _logout_, after which your app will treat them
like any other unauthenticated client. Logging a user out does not require a
call to the API.

### Registering New Credentials

There _is_ an open registration feature where users can sign up for accounts
themselves. However, newly registered accounts that were not created by an
`administrator` will not be allowed to access the system until they are approved
by an `administrator`.

> `administrators` can also create new users accounts manually. When an account
> is created in this way, upon initially logging in, the newly created user must
> choose a new password. Such an account, having been created by an
> `administrator` explicitly, is automatically "approved" upon creation.

When a new user is created, they must provide at least the following:

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
- City
- State
- Zip
- Address

Your app must store all user information locally.

### Additional Constraints

- Usernames and email addresses must be unique within your app. That is: no two
  users can have the same username or email address.
- Users will be prevented from logging in for 1 hour after 3 failed login
  attempts. Users will always see how many attempts they have left.
- Users will have the option to use
  [remember me](https://www.troyhunt.com/how-to-build-and-how-not-to-build)
  functionality to maintain long-running authentication state. That is: if a
  user logs in and wants to be "remembered," they will not be logged out until
  they manually log out.

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

Additionally, the following must always be visible within the navigation
element:

- The total number of elections in the system
- The total number of open elections in the system
- The total number of closed elections in the system

## Requirement 9

**When voting in an election, a `voter` must rank all options in order of
preference. When an election closes, the winner is determined via either
Instant-Runoff Voting (IRV) or the Copeland method (CPL).**

There are a variety of voting algorithms available, each with their pros and
cons. When creating elections, administrators choose the algorithm used to
determine the winner: either
[Instant-Runoff Voting](https://courses.lumenlearning.com/waymakermath4libarts/chapter/instant-runoff-voting)
(IRV) or
[the Copeland method](https://courses.lumenlearning.com/waymakermath4libarts/chapter/copelands-method)
(CPL).

### The Instant-Runoff Voting (IRV) Method

When an eligible voter votes in an IRV election, they do not just cast a single
vote. They must rank each option from most favored to least favored ranked 1 to
_N_, respectively. After the election closes, the system will calculate the
winner by the rules of the IRV algorithm described
[here](https://courses.lumenlearning.com/waymakermath4libarts/chapter/instant-runoff-voting).

There is also a [YouTube video](https://www.youtube.com/watch?v=6axH6pcuyhQ)
explaining Instant-Runoff Voting.

### The Copeland (CPL) Method

When an eligible voter votes in a CPL election, they do not just cast a single
vote. Similar to IRV, they must rank each option from most favored to least
favored ranked 1 to _N_, respectively. After the election closes, the system
will calculate the winner by the rules of the CPL algorithm described
[here](https://courses.lumenlearning.com/waymakermath4libarts/chapter/copelands-method).

There is also a [YouTube video](https://www.youtube.com/watch?v=FftVPk7dqV0)
explaining the Copeland method.

## Requirement 10

**Users view election updates in real time; newly cast votes, open/close status,
calculated winner, et cetera will appear without a page refresh.**

> This requirement only applies to elections that are
> [owned by your team](#owned-elections).

The [Election view](#requirement-3) must present the most up-to-date election
state to users able to view said election. New votes, status updates such as
when the election closes and a winner is selected, and any other relevant data
must eventually appear within this view _without the page refreshing_ or the
user doing anything extra (like pressing a refresh button).

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
to regularly check a source for new data every now and then.

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
> [range queries](https://hscc35a947d8.docs.apiary.io/#/introduction/pagination)
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
[There](https://web.archive.org/web/20230127140833/https://nakedsecurity.sophos.com/2013/11/20/serious-security-how-to-store-your-users-passwords-safely/)
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
> [the API will respond with an `HTTP 555` error](https://hscc35a947d8.docs.apiary.io/#/introduction/rules-of-api-access)
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
