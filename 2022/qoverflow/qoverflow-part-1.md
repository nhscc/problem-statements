# BDPA NHSCC 2022 Problem Statement (part 1)

> See also: [API documentation](https://hscc8udvc7gs.docs.apiary.io)

BDPA wishes to create a platform where students, volunteers, and members can ask
and answer each other's questions, vote on which answers are best, and generally
self-moderate. Your team won the contract to build this platform: _qOverflow_.

<details><summary>Summary of requirements (15 total)</summary>

The app supports two user types: guests and authenticated users. Authentication
occurs via the API. Users interact with each other primarily through the asking
and answering of questions. Users can also provide additional context by
commenting and voting on questions and answers. Users can also send each other
mail messages.

The app has at least five views: _Buffet_, _Question-and-Answer_ (Q&A), _Mail_,
_Dashboard_, and _Auth_. The Buffet view acts as the homepage of the app and the
starting place for viewing and answering the latest questions. The Q&A view is
where users can read details about questions and their answers and related
comments. The Mail view is where users view and exchange messages. The Dashboard
view is where users can configure their accounts and view information specific
to them. The Auth view is used for handling authentication as only authenticated
users can create and answer questions.

Note that time-based data in the API is represented as the number of
milliseconds elapsed since January 1, 1970 00:00:00 UTC. See
[the API documentation](https://hscc8udvc7gs.docs.apiary.io/#/introduction/tips-for-debugging)
for details. Further note that you **must** use the API to complete this problem
statement, including interacting with data from other chapters, though you may
consider a hybrid approach where you have your own database storing non-API
data.

</details>

> We're looking for feedback! If you have any opinions or ideas, contact us on
> Slack.

🚧 🚧 To avoid disqualification, please take note of the following:

- Your solution’s landing page must be available at `http://127.0.0.1:3000`
  (localhost)
- Your solution’s source code must be located at `%USERPROFILE%\Desktop\source`
- HTTP requests to the API must be sent with an `Authorization` header (`Key`
  header is deprecated). See
  [the API documentation](https://hscc8udvc7gs.docs.apiary.io) for details.

## Requirement 1

**Your app will support 2 types of users: guest and authed.**

### Guests

- Are unauthenticated users (i.e. don't have to login).
- Can access the [Auth view](#requirement-6).
- Can access search functionality.
- Can view questions, answers, and comments.
- Cannot vote, give or receive points, create content, view or receive mail, or
  access a dashboard.

### Authed (Authenticated)

- Are authenticated users (i.e. users that have already logged in).
- Can access every view.
- Can access search functionality.
- Can view and create questions, answers, and comments depending on their level.
- Can receive points by creating questions and answers; can eventually give
  points by voting on questions and answers.
- Can attain increasingly powerful "levels" by maintaining a certain threshold
  of points.
- Can access a personalized dashboard.
- Can send and receive personal messages ("mail").
- All new users begin with 1 point, which is their "first point".

#### Levels for Authenticated Users

Users that meet certain point thresholds or "levels" acquire certain privileges.
There are a total of seven (7) levels. Each level includes all of the privileges
of the levels before it.

- **Level 1** _(1 point)_ Create new answers.
- **Level 2** _(15 points)_ Upvote questions and answers.
- **Level 3** _(50 points)_ Create new comments under any question or answer.
  - Normally, users can only comment on their _own_ questions and answers.
    However, at level 3, they can comment on _any_ question or answer.
- **Level 4** _(125 points)_ Downvote questions and answers.
- **Level 5** _(1,000 points)_ View the upvotes and downvotes on a question or
  answer.
  - Normally, users see only the total or "net" number of votes a question or
    answer has received: that's upvotes minus downvotes. However, at level 5,
    users will see the net total in addition to total upvotes and total
    downvotes separately; users will see three numbers instead of just one in
    the [Q&A view](#requirement-3).
- **Level 6** _(3,000 points)_ Trigger and participate in
  [Protection](#protecting-questions-and-closingreopening-questions) votes on
  questions.
- **Level 7** _(10,000 points)_ Trigger and participate in
  [Close/Reopen](#protecting-questions-and-closingreopening-questions) votes on
  questions.

Users with less than 1 point have no level and cannot create new answers.

All `authed` users can create new questions. Regardless of their level, users
can **always** comment on their own questions and answers, and on answers to
their questions.

#### Gaining Points As An Authenticated User

Users gain points by getting upvotes and by asking and answering questions.
Users lose points by getting downvotes. Specifically:

- Every time a user asks a question, the user gets 1 point.
- Every time a user answers a question, the user gets 2 points.
- Every time a user's question gets an upvote, the user get 5 points.
- Every time a user's question gets a downvote, the user loses 1 point.
- Every time a user's answer gets an upvote, the user gets 10 points.
- Every time a user's answer gets a downvote, the user loses 5 points.
- Every time a user downvotes a question or answer, the downvoting user loses 1
  point.
- If a user's answer is chosen as the accepted answer, that user gets 15 points.

Users cannot upvote or downvote their own questions/answers. Users cannot upvote
a question they've already downvoted (and vice-versa) unless they undo their
previous vote first. Users can only issue one vote per entity (e.g. a user
cannot upvote the same question five times). These rules are all enforced at the
API level, so your team does not have to spend much time implementing them.

Additionally, users can undo or "decrement" their upvotes/downvotes. **When a
vote is undone, any points awarded to or taken away from any users will also be
reversed.**

## Requirement 2

**Buffet view: create new questions and view sorted questions.**

This is the "home page" of the app, and is the first page users will land on
when they navigate to your app. The purpose of this view is to show the user the
latest or most recent questions.

From this view, users of the appropriate level and authorization can:

- Create a new question.
- See the 100 most recent questions by default.
  - Users can choose to sort questions by the following four (4) criteria:
    - The most **recent** questions, which are the 100 latest questions. This is
      the default sort order.
    - The **best** questions, which are the top 100 questions that have the
      highest number of upvotes.
    - The most **interesting** questions, which are the top 100 _unanswered_
      questions that have the most upvotes, views, and comments.
    - The **hottest** questions, which are the top 100 questions without an
      _accepted answer_ that have the most upvotes, views, answers, and
      comments.
  - Each question listed will show the following information:
    - The title of the question.
    - Total number of votes (upvotes minus downvotes).
    - Total number of answers.
    - Total number of views.
    - The user who created the question along with their
      [profile picture](#user-profile-pictures) and their level.
    - The date and time when the question was asked.
      - For example: "12/24/2020 12:30:44 PM" _or_ "2 hours ago" (more points).
- Click on a question, which leads to the [Q&A view](#requirement-3) for that
  question.

### Creating A New Question

When creating a new question, an `authed` user must provide the following:

- A question title (API-enforced maximum length 150 characters)
- A question body (API-enforced maximum length 3,000 characters) written in
  [Markdown](https://www.markdownguide.org/getting-started)

When writing the body of a question, the user must be able to see a preview
before they submit it ([example](https://markdown-it.github.io/)). Since
question bodies are written in
[Markdown](https://www.markdownguide.org/getting-started), your app will have to
use a library to render the Markdown into HTML, which you can then display to
your users. Research and explore which Markdown library is best for your
purposes.
[Here are some suggestions to start with](https://npmcompare.com/compare/markdown,markdown-it,marked,remarkable,showdown).

## Requirement 3

**Q&A view: interact with a question and its answers and comments.**

Clicking on a question in the [Buffet](#requirement-2) or
[Dashboard](#requirement-5) views, or attempting to create a new question, leads
to this view, which shows detailed information about the question including any
related answers and comments.

From this view, users of the appropriate level and authorization can:

- Create a new question.
- View when the question was created ("asked").
- View how many views the question has.
- View how many total points (upvotes minus downvotes) the question has.
- View the question title.
- View the question body [rendered as Markdown](#creating-a-new-question).
- View the username of the user that created the question, their
  [profile picture](#user-profile-pictures), and their level.
- View any answers given to the question, each
  [rendered as Markdown](#creating-a-new-question).
  - Along with the rendered Markdown, each answer shows the username of the
    answerer, their [profile picture](#user-profile-pictures), their level, when
    the answer was created, and the total points (upvotes minus downvotes) the
    answer has.
- View any comments under the question. Comments do not support Markdown.
- Add a new answer to the question.
  - **Answers are limited to an API-enforced maximum length of 3,000
    characters.**
  - Users will be able to view preview renders of their Markdown similar to
    [the requirements when creating a new question](#creating-a-new-question).
- Add a new comment under the question, or under any of the answers.
  - **Comments are limited to an API-enforced maximum length of 150
    characters.**
- Upvote or downvote the question.
- Upvote or downvote an answer.

From this view, the user that created the question can "accept" one (and only
one) of the given answers as _the_ answer—even their own answer to their own
question. When an answer is accepted as _the_ answer, it will be clear in the UI
to all users which answer was accepted. Accepting an answer is permanent.
Regardless of which answer was accepted, or if no answer has been accepted yet,
answers will appear in order of most total points to least total points with the
answers with the most points at the top.

Every time a user views a question through this view, the question's "number of
views" will be incremented by one (1).

Users can answer their own questions, and can **always** comment on their own
questions and answers, but cannot vote on (upvote/downvote) their own questions
or answers. Users cannot upvote a question they've already downvoted and
vice-versa. Users cannot vote on a question they've already voted on (e.g.
upvoting multiple times is not allowed); however, users can "undo" their own
votes, after which they are allowed to cast a different vote if they wish. These
rules are all enforced at the API level, so your team does not have to spend
much time implementing them.

Comments will appear in the order that they were created, with the oldest
comments appearing first. Comments will show the comment text followed by the
name of the user that created it.

### Protecting Questions and Closing/Reopening Questions

All questions have a status which is either _Closed_, _Open_ (the default), or
_Protected_.

From this view, level 6 and above users can vote to _Protect_ questions,
including their own questions. Level 7 and above users can vote to _Close_ Open
questions and _Reopen_ Closed questions, including their own questions.
Protected questions cannot be commented on or answered by users who are below
level 5. Closed questions can no longer be commented on or answered by anyone.
Reopened questions are treated as normal questions.

A vote to Protect or Close a question, or Reopen a Closed question, is triggered
by a user of the appropriate level voting "yes" on the action in the UI. Once
this happens, two (2) other users of the appropriate level must also vote "yes"
on the action. Once three (3) users of the appropriate level have registered
"yes" votes, the question's status will change to _Closed_, _Open_ (the
default), or _Protected_ as appropriate.

While a vote to Protect or Close an Open question, or Reopen a Closed question,
is ongoing, the Q&A view will communicate via UI to all users that the question
they're viewing might become Protected/Closed/Reopened along with the usernames
of the users that are currently voting "yes". Users who are currently voting
"yes" can rescind their votes at any time so long as the question's status has
not yet changed. If all users who voted "yes" rescind their votes, the question
will stay at its current status unchanged and any UI communications removed. If
three users do vote "yes" for Protecting or Closing a question, its status will
change appropriately. Upon viewing a question that does not have the _Open_
status, the user will see some indication in the UI that the question is indeed
Protected/Closed and what it means.

Question statuses—_Closed_, _Open_, and _Protected_—are mutually exclusive; a
question can only be one of these things at a time. Protected questions can
become closed, after which they are no longer protected. Closed questions cannot
become Protected, however; they must be Reopened first.

Questions can only have one ongoing vote at a time.

> Keep in mind that other teams' solutions can also edit the status of a
> question. What should your app do if a question with an ongoing Protection
> vote has its status suddenly changed to _Protected_ or _Closed_ by another
> team's app?

## Requirement 4

**Mail view: view, send, and manage private messages between users.**

From this view, `authed` users can:

- View a list of received mail messages.
  - This includes the subject of the message, the message body
    [rendered as Markdown](#creating-a-new-question), and the username of the
    message's sender.
- Send a mail message to another user.
  - Users must specify a message subject, a destination user, and a message body
    [previewable as rendered Markdown](#creating-a-new-question).
  - The message subject must be limited to 75 characters (enforced by API).
  - The message body must be limited to 150 characters (enforced by API).

## Requirement 5

**Dashboard view: `authed` users view information about themselves.**

From this view, `authed` users can:

- See their profile picture.
- View all the questions they've created.
  - Users will see the title of the question and the total number of votes.
  - Clicking a question will lead to the [Q&A view](#requirement-3).
- View all the answers they've created.
  - Users will see the total number of votes their answer has received.
  - Clicking an answer will lead to the [Q&A view](#requirement-3).
- Change their email address.
- Change their password.
- Delete their account.

### User Profile Pictures

All users must have profile pictures provided by
[Gravatar](https://gravatar.com), an API service that associates people's email
addresses with profile pictures of their choosing. To get a user's profile
picture from their email,
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

## Requirement 6

**Auth view: register new users and authenticate existing users.**

`guest` users can use this view to authenticate (login) using their _username_
and their _password_. This sensitive information is referred to as a user's
_credentials_.

### Authenticating Credentials (Login)

Your app must use
[the API](https://hscc8udvc7gs.docs.apiary.io/#/reference/0/user-endpoints/users-username-auth-get)
to authenticate the `guest` user _instead of_ retrieving the user's credentials
from a local database. Your app will do this by sending the API a
[digest value](https://developer.mozilla.org/en-US/docs/Glossary/Digest) derived
from the username and password provided. See
[the API documentation](https://hscc8udvc7gs.docs.apiary.io/#/reference/0/user-endpoints/users-username-auth-get)
for more details.

### Revoking Authentication (Logout)

If `authed`, the user can choose to _logout_, after which your app will treat
them like any other `guest` user. Logging a user out does not require a call to
the API.

### Registering New Credentials

There is an open registration feature `guest` users can use to register a new
account. When they do, they must provide the following where required:

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
[the API](https://hscc8udvc7gs.docs.apiary.io/#/reference/0/user-endpoints/users-post)
to create the new user _instead of_ storing the user's information locally.

### Additional Constraints

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
  credentials, and track any relevant metadata. See
  [the API documentation](https://hscc8udvc7gs.docs.apiary.io/#/data-structures/0/user)
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

**A navigation element containing the BDPA logo, your app title, and a subset of
user data is permanently visible to users.**

In every view, a navigation element is permanently visible containing the BDPA
logo (downloadable
[here](https://bdpa.org/wp-content/uploads/2020/12/f0e60ae421144f918f032f455a2ac57a.png))
and the title of your app. For `guest` users, they also have the option to login
or sign up. For `authed` users, they will see their
[profile picture](#user-profile-pictures) (which links to the
[Dashboard view](#requirement-5)), their current
[level](#levels-for-authenticated-users), and the total number of
[points](#authed-authenticated) they have.

## Requirement 9

**Any user can search for questions in the system.**

Users can search for questions by their title, body text, creator, creation
time, or some combination thereof. Results will appear in descending order of
creation time (latest questions appear first) by default. Users can start a
search from any view in the app.

## Requirement 10

**The total vote count on questions, answers, and comments update in the
[Buffet](#requirement-2) and [Q&A](#requirement-3) views without a page
refresh.**

In the [Buffet](#requirement-2) and [Q&A](#requirement-3) views, updated counts
for the total number of votes (upvotes minus downvotes), total number of
answers, and total number of views must eventually appear in the UI as they
happen _without the page refreshing_ or the user doing anything extra, like
pressing a refresh button.

For example, suppose User A is using the [Q&A view](#requirement-3) and the
question they're viewing currently has 10 net votes. Somewhere else in the
world, User B upvotes that same question. To satisfy this requirement, the
question User A is viewing must eventually update to show 11 votes _without User
A refreshing the page or pressing a button_.

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
> [range queries](https://hscc8udvc7gs.docs.apiary.io/#/introduction/pagination)
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
injection attacks. User-generated outputs _(like the titles/names of items and
the result of rendered Markdown)_ will not be vulnerable to XSS or similar
attacks.**

As for database security, passwords and other credentials used to authenticate
your users **must be stored in the API** and **NEVER stored locally!** Any other
sensitive information present in your local database can be hashed or encrypted
as you deem appropriate.

> You must forward login credentials to the API when attempting to authenticate
> a user. Users' passwords, if stored in your database or app in any form, will
> earn your team zero points for this requirement.

## Requirement 14

**The app will
[fail gracefully](https://getbootstrap.com/docs/5.0/forms/validation/#server-side)
when exceptional conditions are encountered.**

This includes handling API errors during fetch, login errors, random exceptions,
[showing spinners](https://getbootstrap.com/docs/4.4/components/spinners) when
content needs to load, etc.

> Every so often,
> [the API will respond with an `HTTP 555` error](https://hscc8udvc7gs.docs.apiary.io/#/introduction/rules-of-api-access)
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
