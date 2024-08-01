# BDPA NHSCC 2024 Problem Statement (part 2)

> See also: [API documentation](https://hscc35a947d8.docs.apiary.io)

BDPA Elections, Inc. applauds the successful rollout of its IRV-CPL election
system. User feedback indicates users are satisfied with your app UX and
performance data shows response time tail latencies are very low. But your
contractor has identified some changes they want to make to the original app
specification.

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

**In addition to IRV and CPL voting methods from PS1, your app will support the
"Score Then Automatic Runoff" (STAR) method.**

When an eligible voter votes in a
[Score Then Automatic Runoff](https://ballotpedia.org/STAR_voting) (STAR)
election, they do not just cast a single vote. They must rank each option from
most favored (five stars) to least favored (zero stars). After the election
closes, the system will calculate the winner by the rules of this fairly simple
algorithm described [here](https://ballotpedia.org/STAR_voting) and
[here](https://en.wikipedia.org/wiki/STAR_voting#Method).

There is also a [YouTube video](https://www.youtube.com/watch?v=3-mOeUXAkV0)
explaining the Score Then Automatic Runoff method.

When creating an election, administrators can choose to use this algorithm for
determining the winner.

## ✨Change 2✨

**In addition to IRV and CPL voting methods from PS1, your app will support the
"Plurality" or "First Past The Post" (FPTP) method.**

When an eligible voter votes in a
[Plurality](https://courses.lumenlearning.com/waymakermath4libarts/chapter/plurality-method)
election, also known as
[First Past The Post](https://ballotpedia.org/Plurality_voting_system) (FPTP),
the option with the most first-preference votes is the calculated winner. After
the election closes, the system will calculate the winner by the rules of this
very simple algorithm described [here](https://ballotpedia.org/STAR_voting).

> This is the election system used in most of the United States of America
> (except when voting for president, which uses a system that is somehow even
> more terrible).

There is also a [YouTube video](https://www.youtube.com/watch?v=4kfwG1GYIVQ)
explaining the Plurality method.

When creating an election, administrators can choose to use this algorithm for
determining the winner.

## ✨Change 3✨

**History view update: users can view what the result of a past election would
have been under a different voting method.**

With this change, users must be presented with _the option_ to display how the
election would have gone using a different voting method. This is an entirely
client-side affair in that using this option to see the result under a different
voting method must have absolutely no effect on who won the election or any
other server-side effects.

Your team will have to determine how to deal with data inconsistencies between
the voting methods.

For example, suppose a user viewing an election that used FPTP wanted to see who
would win if the election were using IRV instead. Each ballot of the FPTP
election might only have a single selection since FPTP
[is not a preference-based voting method](https://courses.lumenlearning.com/waymakermath4libarts/chapter/plurality-method)
(i.e. `voters` pick their favorite from a list of choices instead of ranking the
choices). So attempting to see who would win a FPTP election if it were actually
an IRV or STAR election might not be possible. This should be indicated in the
UI.

Another example would be trying to show how an IRV election would have gone
under the rules of the STAR method. IRV allows `voters` to rank the choices from
1 to _N_ while STAR only allows `voters` to rank choices from 1 to 5. Further,
in STAR-based elections, `voters` can give multiple options the same ranking
whereas all options must have a unique ranking in IRV. You will have to
determine how to handle this. A suggestion:
[normalize](https://github.com/nhscc/learner-resources/blob/main/examples/normalize.js)
the IRV rankings into STAR rankings, and then run the STAR algorithm.

A third example could be the reverse of the first: seeing who would win if an
IRV election were using FPTP instead. IRV-to-FPTP is trivial to implement: it's
just counting all the rank-1 choices from the IRV ballots and seeing which one
got the highest score.

## Change 4

**History and Election view updates: more descriptive results.**

Before this change, a user was shown the winner of an election they're viewing
(if it's closed) in the _[History](bdpa-elections_cpl-part-1.md#requirement-5)
and [Election](bdpa-elections_cpl-part-1.md#requirement-3)_ views. The rest of
the UI was left up to the developer. Now, when viewing elections, users must be
presented with _the option_ to display at least the following information:

### Open and Closed Elections

- The total number of eligible `voters` who voted in the election versus the
  total number of eligible `voters`; an "eligible voter" is a `voter` that has
  been assigned to the election in question
- For each option in the election:
  - What the option's current IRV/CPL/STAR/FPTP rank/result is
    - That is: in which round the option was eliminated for IRV and what the
      option's total score was for CPL, STAR, and FPTP
    - Sorted in descending order of elimination for IRV, or descending order of
      score for CPL, STAR, and FPTP, where the option that's winning is at the
      top
    - The currently winning option if the election is open, or the winner if the
      election is closed, will be emphasized in the UI in some way
  - The total number of `voters` whose ballots have this option as the highest
    rank and/or the largest number of votes (for FPTP)

For example, suppose `Election #1` with options `["red", "green", "blue"]`, that
uses the IRV method, has the following ballots:

```json
[
  {
    "voter_id": "voter1_<timestamp>",
    "ranking": { "green": 1, "blue": 2 }
  },
  {
    "voter_id": "voter2_<timestamp>",
    "ranking": { "blue": 1 }
  },
  {
    "voter_id": "voter3_<timestamp>",
    "ranking": { "blue": 1, "green": 2 }
  },
  {
    "voter_id": "voter4_<timestamp>",
    "ranking": { "blue": 1, "green": 2, "red": 3 }
  },
  {
    "voter_id": "voter5_<timestamp>",
    "ranking": { "red": 1, "blue": 2, "green": 3 }
  },
  {
    "voter_id": "voter6_<timestamp>",
    "ranking": { "red": 1, "green": 2 }
  },
  {
    "voter_id": "voter7_<timestamp>",
    "ranking": { "red": 1 }
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

These are just examples. How you decide to communicate this type of information
to the user is entirely up to your team. Note also how the shape of the ranking
is entirely up to your team, including if all options must be ranked or not.
**Keep this in mind when considering how best to render elections created and
maintained by other teams.**

## Change 5

**`administrators` can make elections public.**

Before this change, no part of the system was accessible to clients that had not
yet authenticated. Therefore, each election in the system was considered
_private_. Now, an `administrator` has the choice to make an election _public_.

A _public election_ is an election whose results can be viewed by anyone
regardless of if they are logged in or not. All other rules governing elections
still apply, i.e.: what information is shown/hidden and from whom, users must
still be authenticated as `voters` to vote in elections, etc.

## Change 6

**Maintenance mode.**

The
[super administrator user](./bdpa-elections_cpl-part-1.md#the-super-administrator-user)
can, from their dashboard, toggle the app into and out of a "read-only" mode,
also known as
["maintenance mode"](https://www.quora.com/Why-do-some-sites-have-maintenance-mode-regularly-i-e-Stack-Overflow-which-is-unavailable-for-periods-of-time-and-some-sites-do-not-like-Google-or-Facebook-which-are-always-available-unless-some-error-occurs-in),
where no users other than `administrators` can make changes to the system. This
means `voters` cannot vote in elections, even if they otherwise could, when the
system is in maintenance mode.

The specific implementation of "maintenance mode" that is most optimal for your
application is left up to your team's best judgement.

## Change 7

**Dashboard view update: expiration warnings.**

Elections a `voter` is eligible to vote in _that they haven't voted in yet_ that
are closing _in an hour or less_ will be specially marked in the UI somehow. The
goal is to alert `voters` that an election they should have voted in is about to
end.

## Change 8

**Election view update: election audit log.**

Before this change, a user was shown the winner of an election they're viewing
(if it's closed) in the
[Election view](bdpa-elections_cpl-part-1.md#requirement-3). The rest of the UI
was left up to the developer. Now, when viewing an individual election, users
must be presented with _the option_ to display an "audit log" of the election.

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

## Change 9

**`administrators` can view voters' real names.**

Previously, in the [History view](bdpa-elections_cpl-part-1.md#requirement-5)
and the [Election view](bdpa-elections_cpl-part-1.md#requirement-3), the names
of `voters` are explicitly _not_ displayed. This allowed us to preserve our
users' privacy.

However, BDPA Elections, Inc. has determined there is a lot of money to be made
selling this personal information to interested third parties. So,
`administrators` and only `administrators`, upon accessing these views, will now
see the names of `voters` associated with their votes in an aesthetically
pleasing manner.

In the case that a voter's real name is not available, such as when displaying
elections unowned by your team, showing the voter's ID is sufficient.

## Change 10

**Human-friendly timestamps and timezone locale awareness.**

When displaying times like "created at" or "last login time" or "closes at"
anywhere in the app, instead of showing a timestamp or a full date, show a more
"human-friendly" relative time like "9 years ago," "20 minutes ago," "in a day,"
"tomorrow at 12:20 PM," "thursday at 2:15 PM," or whatever your team believes
looks the most human-friendly.

If your solution already does this, good job!
