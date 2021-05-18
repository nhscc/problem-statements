# BDPA NHSCC 2019 Problem Statement (part 2)

> See also: [API documentation](https://hscc4cfe8be7.docs.apiary.io)

BDPA Elections, Inc. applauds the successful rollout of its IRV election system!
User feedback indicates users are satisfied with your app UX and performance
data shows response time tail latencies are very low. But your contractor has
identified some changes they want to make to the original app specification.

There are 10 changes:

## Change 1

**New admin-only view: election audit log and undo/redo.**

There must be a new view accessible only to administrators that shows a list of
all the times an election's information (specifically: `title`, `description`,
`opens`, `closes`, `deleted`) was modified by an administrator account.

Further, administrators will be able to trigger a per-election "undo" which
reverses the most recent change to an election's information (if any admin
changes have been made). If an administrator triggers an "undo" and then
triggers a second "undo" right after, it will redo the change that was undone
with the first undo.

When triggering an undo, administrators will be warned that they're reversing an
election's most recent changes and must be given the choice to cancel the
operation before executing.

> This change only applies to elections created by your API key.

## Change 2

**History view update: election popularity.**

Users will be able to sort paginated elections by "popularity," which is defined
here as "the total number of votes cast". That is: the most popular election is
the election with the most voters having voted in it. Users will be able to sort
the elections in the system by their popularity so that the most or least
popular elections show up at the top of the list on demand.

## Change 3

**Dashboard view update: expiration warnings.**

Elections a voter is eligible to vote in _that they haven't voted in yet_ that
are closing _in an hour or less_ will be specially marked in the UI somehow. The
goal is to alert voters that an election they should have voted in is about to
end.

## Change 4

**Dashboard view update: security audit log.**

Users will be able to see the last five times their account was logged into and
from which IP. Before this change, they were only able to see info on the most
recent time their account was logged into.

## Change 5

**Election view update: more descriptive IRV results.**

Before this change, an authorized user is shown the winner of an election
they're viewing (if it's closed). The rest of the election view UI was left up
to the developer. Now, when viewing elections, authorized users must be
presented with at least the following information if they are not already:

### Upcoming elections

- When the election opens if the election is not open yet

### Open and closed elections

- The total number of eligible voters who voted in the election versus the total
  number of eligible voters.
- For all possible choices in the election, show:
  - What the choice's IRV result is (sorted in descending order)
    - Next to that number, also show the total number of voters who picked it as
      their first choice in the beginning
  - Which choice is currently winning if the election is open or which choice
    has won if the election is closed

For example, suppose `Election #1` with options `["red", "green", "blue"]` has
the following voter data:

```JSON
[
    { "voter_id": "voter1_<timestamp>", "ranking": ["green", "blue"] },
    { "voter_id": "voter2_<timestamp>", "ranking": ["blue"] },
    { "voter_id": "voter2_<timestamp>", "ranking": ["blue", "green"] },
    { "voter_id": "voter3_<timestamp>", "ranking": ["blue", "green", "red"] },
    { "voter_id": "voter4_<timestamp>", "ranking": ["red", "blue", "green"] },
    { "voter_id": "voter5_<timestamp>", "ranking": ["red", "green"] },
    { "voter_id": "voter6_<timestamp>", "ranking": ["red"] },
]
```

If this election were open, the UI would communicate to the user something like
the following:

> Currently, choice `blue` is winning with 4 votes (3 voters chose it as their
> first choice); choice `red` is in second place with 3 votes (3 voters chose it
> as their first choice); choice `green` is in third place (eliminated in round
> one) (1 voter chose it as their first choice)

If this election were closed, the UI would communicate to the user something
like the following:

> Official results: choice `blue` won with 4 votes (3 voters chose it as their
> first choice); choice `red` came in second place with 3 votes (3 voters chose
> it as their first choice); choice `green` came in third place (eliminated in
> round one) (1 voter chose it as their first choice)

How you decide to communicate this information to the user is entirely up to
your team.

## Change 6

**Election, dashboard, and history view updates: the "star" button.**

Elections can now be "starred" (and "un-starred") by users. Show starred
elections first when a user views the election history view. When viewing an
election/on the election view, show the number of users who currently have the
election starred. When a user is viewing their dashboard, show their starred
elections first.

## Change 7

**Human-friendly timestamps and timezone locale awareness.**

When displaying times like "created at" or "last login time" or "closes at"
anywhere in the app, instead of showing a timestamp or a full date, show a more
"human-friendly" relative time like "9 years ago," "20 minutes ago," "in a day,"
"tomorrow at 12:20 PM," "thursday at 2:15 PM," etc.

## Change 8

**Open registration.**

Unauthenticated users can now "sign up" for accounts themselves without asking
an administrator to do it for them. Newly registered accounts will not be
allowed to access the system until they are approved by an administrator.

Administrators can still create new accounts manually.

## Change 9

**CAPTCHA protections for logins and registration.**

The login and new registration views must be protected by a CAPTCHA of some
kind. Do not use an external API like reCAPTCHA for this, you must build your
own. It can be as simple (text-based like `1 plus 1 = ?`) or as complex
(image-based and fancy) as you can imagine so long as it is not trivial for a
bot to defeat.

## Change 10

**Limit vote changes on open elections.**

Before this change, voters could change their vote at any point up until the
election they voted in closed. Now, voters will have only five minutes or until
the election closes (whichever is sooner) to change their vote after submitting
it. After five minutes, their vote will become immutable.
