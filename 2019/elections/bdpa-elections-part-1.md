# BDPA NHSCC 2019 Problem Statement (part 1)

> See also: [API documentation](https://hscc4cfe8be7.docs.apiary.io)

In recent years, adversaries with unlimited money and means are waging a
non-stop global cyber war against every machine connected to the internet. BDPA
Elections, Inc. has been contracted by a billion dollar entity to build a secure
electronic election system to their specifications. The system will allow an
entity to host elections at scale without requiring voters to appear at a
physical voting location.

<details><summary>Summary of requirements</summary>

Your system will consist of a web portal where, _only \_after logging in_,\_
users can view the results of past elections and vote in current elections. You
must query election data by making REST requests to the
[elections API](https://hscc4cfe8be7.docs.apiary.io). **Note that your app must
be able to display the election data from other chapters, including the results
of elections**. Any data you get back from the API is guaranteed to be properly
formatted, so this should not be a problem. Additionally, you are only able to
modify the election data of elections that were created with your
[API key](https://hscc4cfe8be7.docs.apiary.io/#/introduction/requesting-a-key).
All other election data returned from the API is read-only.

There are four _types_ of users: **voters**, **moderators**, **administrators**,
and **reporters**. Users can only be one type at a time. Voters are the most
common type of user. They vote in elections and can view a complete listing of
past election results. Moderators manage elections to determine which voters are
allowed to vote in which elections. Administrators, along with having
system-wide moderator privileges, can manage which users have moderator
privileges as well as create new elections and manage existing ones.
Administrators are also the only type that can create new users, _restrict_
other users, and change a user's type. However, administrators cannot modify the
account details of other administrators or give other users the administrator
type. The **root** user is a special administrator that can give other users the
administrator type and view information that is normally private. There is only
one root user in the system. Finally, reporters can view the history of past
election results and nothing else.

Elections have timestamps (represented as the number of milliseconds elapsed
since January 1, 1970 00:00:00 UTC) marking when they open to allow voting and
when they close and permanently commit their results. Voters can only vote in
open elections, though they can also change their vote rankings as many times as
they want up until the election is closed. Reporters can only see closed
elections.

</details>

> We're looking for feedback! If you have any opinions or ideas,
> [start a discussion](https://github.com/nhscc/problem-statements/discussions/new).

## Requirement 1

**Login: a user must authenticate before they can access the system.**

Authenticating (logging in) is required to access any component of the system.
No part of the system beside the login view will be accessible to
unauthenticated users. Any authenticated _unrestricted_ user has the option to
view their own dashboard and a history of past elections (below).

## Requirement 2

**The system will support 4 types of users: voter, moderator, administrator, and
reporter.**

**Voters**

- View a complete paginated listing of elections
  - This includes elections created by other teams
  - This includes all past elections
  - This includes currently open and future elections that the voter can vote in
  - This does not include deleted elections
- Vote in elections
  - Only for elections created by your
    [API key](https://hscc4cfe8be7.docs.apiary.io/#introduction/requesting-a-key)

**Moderators**

- View a complete paginated listing of past election results
  - This includes elections created by other teams
  - This includes all past elections
  - This includes currently open and future elections the moderator is
    moderating
  - This does not include deleted elections
- Manage elections to determine which voters are allowed to vote in which
  elections
  - Only for elections created by your
    [API key](https://hscc4cfe8be7.docs.apiary.io/#introduction/requesting-a-key)

**Administrators**

- View a complete paginated listing of elections
  - This includes elections created by other teams
  - This includes deleted elections
  - Essentially: this includes all elections in the system
- The following apply only to elections created by your
  [API key](https://hscc4cfe8be7.docs.apiary.io/#introduction/requesting-a-key):
  - App-wide moderator privileges
  - Can manage which moderators moderate which elections
  - Can create new elections and un/delete existing elections
  - Can change information about existing elections
- Can view a list of, create, update, un/restrict, and un/delete users
  - This includes changing a user's type
  - Deleted users should still show up in the list of users
- **Cannot** modify the user data of other administrator type users
- **Cannot** give other users the Administrator type

**Reporters**

- Can view the history of past (closed) election results and nothing else

Users can only have a single type at a time. You are free to create other types.

Note the special **root** user. The **root** user is a special administrator
that can give other users the Administrator type and view information in your
app that might normally be private. The root user is always the first user in
the system and there can only be one root user.

## Requirement 3

**The system will support elections.**

An election has at least the following components:

- An autogenerated globally unique identifier
- A title
- A description
- A unix epoch timestamp indicating when the election was created in the system
- A unix epoch timestamp indicating when the election opens
- A unix epoch timestamp indicating when the election closes
- A boolean representing if the election was deleted or not
- An array of options voters must rank from most favored to least favored
- An array of objects that maps voters to their rankings
  - For example:
    `[{ voter_id: 'somegal425', ranking: ['peanut butter', 'jelly', 'butter'] }, { voter_id: 'someguy3312', ranking: ['butter', 'jelly', 'peanut butter'] }]`
  - Check out the
    [API data structure reference](https://hscc4cfe8be7.docs.apiary.io/#/data-structures/0/vote)
    for more example inputs and outputs

> Warning: All of the above information **must** be stored using the API. You
> can cache it locally, but it **must** also be put into the API. Only accessing
> your local database without using the API will disqualify your solution.

Feel free to add any other information necessary, but extra information cannot
be stored using the API. We recommend you store at least the following
information in your own database:

- A mapping between voters and the elections they're eligible to vote in
- A mapping between moderators and the elections they moderate

Users can view any closed elections returned by the API, _even if they were not
created by your app_. Users interact differently with elections depending on
their type. For elections owned by your app: moderators can add voters to
elections or remove them from elections; administrators can **create/delete**
elections, and change **some parts** of an election; voters vote in these
elections. All users can view the results of _all_ closed elections in the
system, however.

When viewing election results that are closed, the winning choice will be
emphasized in the frontend UI. All eliminated choices will be clearly marked.
The UI will indicate by how many votes the winning choice won versus the total
number of votes cast. Finally, if the election is closed and the user viewing it
voted in said election, their choice in the election will be most prominently
marked.

> Warning: When displaying the results of an election, **you must protect the
> identities of your voters**! Do not reveal who voted for which options in the
> UI, just show aggregate results.

Administrators must be able to delete open/upcoming elections created by your
app. They must be able to edit their titles or descriptions. When using the API
to update your elections based on the actions of administrators, you must decide
what edge cases like changing an election's _available options_, _opening time_,
and _closing time_ means after an election has already opened, already closed,
or has already accepted votes. **You are free to deal with these and any other
edge cases in _any way you see fit_**.

Additionally, deleted elections should only be visible to administrators. To
other user types in the system, deleted elections should be treated as if they
don't exist at all.

## Requirement 4

**Dashboard: each user has access to a personalized user dashboard.**

When viewing their own personalized dashboard, users are presented with the
following information at least once:

- **name**: the name of the user, like "Ray" or "Ray Tiles"
- **last_login_ip**: the IP address that was last used to login as this user
- **last_login_datetime**: a timestamp taken the last time the user was
  authenticated

You are free to display any other relevant information.

All users will have the ability to view the details and results of any elections
that appear in their dashboard. When displaying non-closed elections in the
frontend UI, they will be sorted in ascending order by their opening time
(elections that opened/will open earlier in time are shown first). When
displaying past elections, they will be sorted in descending order by their
closing time (elections that have closed later in time are shown first).

**For voters**

The dashboard will show:

- The most recent _open_ elections the user can currently participate in
- _Closed_ elections they were eligible to participate in
- _Upcoming_ elections they're eligible to participate in

**For moderators**

The dashboard will show:

- All the elections that the moderator has been assigned to oversee
- Moderators will be able to add a user to an election or remove a user from an
  election

**For administrators**

- Along with the controls moderators have, the dashboard will allow
  administrators to view and modify users and elections in the system

## Requirement 5

**History: each user can view a complete history of past elections that can be
sorted by at least the following: title, creation time, opening time, closing
time.**

> Other useful metrics might include allowing users to sort elections by
> ownership (i.e. only showing elections created by your app and not others),
> sorting by deleted status, etc. You might even allow for full-text searching
> of titles and descriptions and sort based on the results of users' searches.

To satisfy this requirement, you'll have to make many multiple calls to the
[`GET /elections`](https://hscc4cfe8be7.docs.apiary.io/#reference/0/metadata-endpoint/list-all-elections-in-the-system)
endpoint at some point to search through and sort all elections in the system.
Consider using a short-lived (30 second to 10 minute) caching strategy to reduce
load on your app and the API. Take care to mind the API's
[rate limits](https://hscc4cfe8be7.docs.apiary.io/#introduction/rate-limits).

## Requirement 6

**When creating new accounts, administrators must provide the following: a
username and a _secure_ password. When logging into a new account for the first
time, the user must be prompted to change their password. If a user forgets
their credentials, there must be some way for them to reset their password.**

There is no open registration feature. Only administrators can create new
accounts. Users' _usernames_ cannot be changed after the account is created
except by administrators. All other information (including full name) can be
modified by the user that owns the account.

Other user information an administrator might provide: **full name (first and
last), an email, phone number, city, state, zip, or address**. These should be
optional for the administrator.

When an account logs in for the first time, they should be prompted to change
their password before they can interact with the rest of the system.

If a user (who has an email address associated with their account) has forgotten
their login credentials, there should be some way for them to be recovered. This
can be via sending the user an email, security questions, etc.

> Note: your system does not have to (and probably should not) send actual
> emails. Log the recovery messages to standard output or a logging file
> instead.

> Note: the **only** requirement for a secure password is that it is
> sufficiently long. 6-10 characters is weak. 11-16 is medium. 17+ is strong.
> Passwords should at least be _medium_ security to pass.

## Requirement 7

**Security: most user information will be private to that user.**

User information that is not private and can be displayed publicly on the
website:

- Username
- Type

> Note: generally, usernames should not be associated with vote results. Protect
> your voters' identities!

User information that no one other than its owner and an administrator can see
and modify:

- Password (administrators **can't** see raw passwords but they can manually
  update them)
- Email
- City
- State
- Zip
- Address
- Phone number

> Note: only the root account can modify the information of an administrator
> that isn't themselves. Administrators cannot modify each other's information,
> but they can view it. Everyone can modify their own information.

## Requirement 8

**User accounts can be _restricted_ or _unrestricted_.**

A _restricted_ user is not allowed to login. An _unrestricted_ user is allowed
to login. Only administrators can un/restrict users. Users can _never_ be both
an administrator and restricted.

By default, new users will be unrestricted. Restricting and unrestricting a user
will not alter the results of any elections in the system in any way. If a user
is logged in and their account becomes restricted, **they will be forced to log
out when they next interact with the app.**

## Requirement 9

**Closed elections are immutable.**

Once an election is closed, its results become immutable, which means: none of
the information about that election can be modified by administrators and the
election itself cannot be deleted by administrators. The root user is exempt
from these restrictions; they can delete closed elections or modify some parts
of them (title, description, deleted flag). Regardless, the outcomes of closed
elections can never be modified.

This also implies that if a user voted in an election before their account was
deleted, their vote must still count and the outcome of the election cannot
change.

## Requirement 10

> During the competition, this was requirement #12.

**Somewhere in the UI of every<sup>1</sup> view, the total number of elections
in the system will always be visible.**

<sup>1</sup>There are some exceptions to this, like the login page where showing
the total number of elections would be a dangerous information leak.

## Requirement 11

**When voting in an election, a voter must rank all choices in order of
preference. When an election closes, the winner is determined via Instant-Runoff
Voting.**

When an eligible voter votes in an
[Instant-Runoff Voting](https://courses.lumenlearning.com/waymakermath4libarts/chapter/instant-runoff-voting)
(IRV) election, they do not just cast a single vote. They must rank their
choices from most favored to least favored ranked 1 to _N_, respectively. After
the election closes, the system will calculate the winner by the rules of the
IRV algorithm:

1. All the top choices (meaning: rank 1) are counted.
2. If a choice gets over 50% of the vote, that choice is declared the winner and
   the election is over.
3. If no choice gets under 50% of the vote, the choice with the least rank 1
   votes is eliminated.
4. Voters who had the eliminated choice as their rank 1 have their vote go to
   their next top choice instead (meaning: their rank 2 becomes their new rank
   1).
5. Return to step 1 and repeat the process until only one choice remains or a
   choice gets more than 50% of the votes.

For example, suppose an administrator created an election titled _What should we
eat after the competition_? The administrator adds three choices to vote for:
pizza, chicken, and tacos. Further suppose there were 10 eligible voters. Voter
1 ranks the choices according to their tastes:

<table>
    <tr>
        <td colspan="2">1 voter's choices</td>
    </tr>
    <tr>
        <td>Rank</td>
        <td>Choice</td>
    </tr>
    <tr>
        <td>1</td>
        <td>Pizza</td>
    </tr>
    <tr>
        <td>2</td>
        <td>Tacos</td>
    </tr>
    <tr>
        <td>3</td>
        <td>Chicken</td>
    </tr>
</table>

Clearly, voter 1's favorite choice is Pizza, their second favorite is Tacos, and
their least favorite is Chicken.

The other nine voters come up with their own ranks for the choices as well.
Since many of them voted similarly to each other, the 10 different voters'
rankings can be summarized as the following:

<table>
    <tr>
        <td>
            <table>
                <tr>
                    <td colspan="2">4 voters voted like this</td>
                </tr>
                <tr>
                    <td>Rank</td>
                    <td>Choice</td>
                </tr>
                <tr>
                    <td>1</td>
                    <td>Chicken</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Pizza</td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>Tacos</td>
                </tr>
            </table>
        </td>
        <td>
            <table>
                <tr>
                    <td colspan="2">4 voters voted like this</td>
                </tr>
                <tr>
                    <td>Rank</td>
                    <td>Choice</td>
                </tr>
                <tr>
                    <td>1</td>
                    <td>Pizza</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Tacos</td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>Chicken</td>
                </tr>
            </table>
        </td>
        <td>
            <table>
                <tr>
                    <td colspan="2">2 voters voted like this</td>
                </tr>
                <tr>
                    <td>Rank</td>
                    <td>Choice</td>
                </tr>
                <tr>
                    <td>1</td>
                    <td>Tacos</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Pizza</td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>Chicken</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

If we just counted who got the most rank-1 votes (like a normal election),
_Chicken_ and _Pizza_ would be tied for first place and no one would win.
However, we're using **Instant-Runoff Voting!**

So, since no one got above 50% of the votes (50% of 10 is 5, so a choice needs 6
votes to win), we **eliminate the choice with** **the least rank-1 (first place)
votes.** Since Tacos only got 2 rank-1 votes, Tacos is eliminated. After running
step #4 in the IRV algorithm, now the rankings look like this:

<table>
    <tr>
        <td>
            <table>
                <tr>
                    <td colspan="2">4 voters voted like this</td>
                </tr>
                <tr>
                    <td>Rank</td>
                    <td>Choice</td>
                </tr>
                <tr>
                    <td>1</td>
                    <td>Chicken</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Pizza</td>
                </tr>
            </table>
        </td>
        <td>
            <table>
                <tr>
                    <td colspan="2">4 voters voted like this</td>
                </tr>
                <tr>
                    <td>Rank</td>
                    <td>Choice</td>
                </tr>
                <tr>
                    <td>1</td>
                    <td>Pizza</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Chicken</td>
                </tr>
            </table>
        </td>
        <td>
            <table>
                <tr>
                    <td colspan="2">2 voters voted like this</td>
                </tr>
                <tr>
                    <td>Rank</td>
                    <td>Choice</td>
                </tr>
                <tr>
                    <td>1</td>
                    <td>Pizza</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Chicken</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

Chicken still has 4 votes, but now pizza has 6 votes. Since Pizza has more than
50% of the votes, the system indicates choice Pizza wins the election.

> [There is also a YouTube video explaining Instant-Runoff Voting](https://www.youtube.com/watch?v=6axH6pcuyhQ)

## Requirement 12

> During the competition, this was requirement #10.

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
> [the API will respond with an `HTTP 555` error](https://hscc4cfe8be7.docs.apiary.io/#/introduction/rules-of-api-access)
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
