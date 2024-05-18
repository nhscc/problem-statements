# BDPA NHSCC 2024 Problem Statement (part 2)

> See also: [API documentation](https://hscc18f802d3.docs.apiary.io)

BDPA Elections, Inc. applauds the successful rollout of its IRV election system.
You have served democracy well! User feedback indicates users are satisfied with
your app UX and performance data shows response time tail latencies are very
low. But your contractor has identified some changes they want to make to the
original app specification.

> We're looking for feedback! If you have any opinions or ideas, contact us on
> Slack.

🚧 🚧 To avoid disqualification, please take note of the following:

<details><summary>Expand</summary>

- **Any evidence of illegal remote access to AWS WorkSpaces or any other AWS
  resource will result in immediate disqualification.**

- As with AWS, any evidence of outside assistance (e.g. commits, offline
  patches, ongoing annex sync, pulls, remotes, reflog chatter, uploads, etc)
  during this phase of the competition will result in immediate
  disqualification. Unlike PS1, **only the three to five students on your
  competition team—and no one else—can work on PS2.**

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

There are ten (10) changes in total. Three (3) ✨changes✨ are worth more points
than the others:

There are 10 changes:

## ✨Change 1✨

**Election view update: election audit log.**

Before this change, a user was shown the winner of an election they're viewing
(if it's closed) in the Election view. The rest of the UI was left up to the
developer. Now, when viewing an individual election, users must be presented
with _the option_ to display an "audit log" of the election.

This log will show a list of all the times the election's metadata (`title`,
`description`, `opensAt`, `closesAt`, `deleted`) was modified by an
administrator. Additionally, anytime a new ballot is cast, or a ballot is
changed or deleted, along with any other such changes must also be included in
the log.

> Remember: voters' names must _never_ be shown unless explicitly instructed
> otherwise in the problem statement.

The audit log can be viewed by any authenticated user. However, it need only be
available for elections owned by your team. **You do not need to track changes
or present an audit log for elections that are not owned by your team.**

## ✨Change 2✨

**History and Election view updates: more descriptive IRV results.**

Before this change, a user was shown the winner of an election they're viewing
(if it's closed) in the _History and Election_ views. The rest of the UI was
left up to the developer. Now, when viewing elections, users must be presented
with _the option_ to display at least the following information:

### Upcoming Elections

- When the election opens if the election is not open yet

### Open and Closed Elections

- The total number of eligible `voters` who voted in the election versus the
  total number of eligible `voters`
- For each option in the election:
  - What the option's current IRV rank/result is (i.e. in which round the option
    was eliminated)
    - Sorted in descending order of elimination with the option that is
      currently winning at the top
    - The currently winning option if the election is open, or the winner if the
      election is closed, will be emphasized in the UI in some way
  - The total number of `voters` whose ballots have this option as the highest
    rank (i.e. rank 1)

For example, suppose `Election #1` with options `["red", "green", "blue"]` has
the following ballots:

```json
[
  {
    "voter_id": "voter1_<timestamp>",
    "ranking": { "1": "green", "2": "blue" }
  },
  {
    "voter_id": "voter2_<timestamp>",
    "ranking": { "1": "blue" }
  },
  {
    "voter_id": "voter3_<timestamp>",
    "ranking": { "1": "blue", "2": "green" }
  },
  {
    "voter_id": "voter4_<timestamp>",
    "ranking": { "1": "blue", "2": "green", "3": "red" }
  },
  {
    "voter_id": "voter5_<timestamp>",
    "ranking": { "1": "red", "2": "blue", "3": "green" }
  },
  {
    "voter_id": "voter6_<timestamp>",
    "ranking": { "1": "red", "2": "green" }
  },
  {
    "voter_id": "voter7_<timestamp>",
    "ranking": { "1": "red" }
  }
]
```

If this election were open, the UI would communicate to the user something like
the following:

> Currently, option `blue` is winning with 4 votes (3 `voters` chose it as their
> first choice); option `red` is in second place with 3 votes (3 `voters` chose
> it as their first choice); option `green` is in third place (eliminated in
> round one) (1 `voter` chose it as their first choice)

If this election were closed, the UI would communicate to the user something
like the following:

> Official results: option `blue` won with 4 votes (3 `voters` chose it as their
> first choice); option `red` came in second place with 3 votes (eliminated in
> the final round) (3 `voters` chose it as their first choice); option `green`
> came in third place (eliminated in round one) (1 `voter` chose it as their
> first choice)

How you decide to communicate this information to the user is entirely up to
your team. Note also how the shape of the ranking is entirely up to your team,
including if all options must be ranked or not. **Keep this in mind when
considering how best to render elections created and maintained by other
teams.**

## Change 3

**Dashboard view update: expiration warnings.**

Elections a `voter` is eligible to vote in _that they haven't voted in yet_ that
are closing _in an hour or less_ will be specially marked in the UI somehow. The
goal is to alert `voters` that an election they should have voted in is about to
end.

## Change 4

**Dashboard view update: security audit log.**

Users will be able to see the last five times their account was logged into and
from which IP. Before this change, they were only able to see info on the most
previous time their account was logged into.

## Change 5

**Dashboard view update: users can edit their own information.**

Users can edit their personal information, including their name, email,
password, and other metadata from dashboard without going through an
`administrator`.

## Change 6

**Reporters must be assigned to elections before they can view them.**

Previously `reporters` and `voters` could view any past election in the system.
Now, `administrators` and `moderators` must assign a `reporter` to an election
before they can view the results.

Elections to which a `reporter` is not assigned cannot be viewed by that user.
Similarly, elections to which a `voter` is not assigned cannot be viewed by that
user.

## ✨Change 7✨

**Administrators can view voters' real names.**

Previously, in the [History view](bdpa-elections_irv-part-1.md#requirement-5)
and the [Election view](bdpa-elections_irv-part-1.md#requirement-3), the names
of voters are explicitly _not_ displayed. This allowed us to preserve our users'
privacy.

However, BDPA Elections, Inc. has determined there is a lot of money to be made
selling this personal information to interested third parties. So,
`administrators` and only `administrators`, upon accessing these views, will now
see the names of voters associated with their votes in an aesthetically pleasing
manner.

## Change 8

**Human-friendly timestamps and timezone locale awareness.**

When displaying times like "created at" or "last login time" or "closes at"
anywhere in the app, instead of showing a timestamp or a full date, show a more
"human-friendly" relative time like "9 years ago," "20 minutes ago," "in a day,"
"tomorrow at 12:20 PM," "thursday at 2:15 PM," etc.

If your solution already does this, good job!

## Change 9

**Open registration.**

Unauthenticated users can now "sign up" for accounts themselves without asking
an administrator to do it for them. Newly registered accounts will not be
allowed to access the system until they are approved by an administrator.

Administrators can still create new accounts manually.

## Change 10

**Limit vote changes on open elections.**

Before this change, `voters` could change their vote at any point up until the
election they voted in closed. Now, `voters` will have only five minutes, or
until the election closes (whichever is sooner), to change their vote after
submitting it. After five minutes, they will be unable to change or remove their
vote.
