# BDPA NHSCC 2025 Problem Statement (part 2)

> See also: [API documentation](https://hscc8udvc7gs.docs.apiary.io)

Management applauds the successful rollout of qOverflow! Feedback indicates
users are satisfied with your app UX and performance data shows response time
tail latencies are very low. But your contractor has identified some changes
they want implemented.

> We're looking for feedback! If you have any opinions or ideas, contact us on
> Slack.

🚧 🚧 To avoid disqualification, please take note of the following:

- We know how to use Git and other tools. If we notice any commits, patches,
  uploads, file transfers, chatter, etc external to your immediate team members
  during this phase of the competition, your team will be disqualified. Unlike
  PS1, **only the three to five students on your competition team—and no one
  else—can work on PS2.**
- Your solution’s landing page must be available at `http://127.0.0.1:3000`
  (localhost) on your team's AWS WorkSpace. **Judges must not have to type in
  anything other than `http://127.0.0.1:3000` to reach your app.**
- Your solution’s source code must be located at `%USERPROFILE%\Desktop\source`
  on your team's AWS WorkSpace. You can have other files located elsewhere, so
  long as they are also visible from the aforesaid directory.
- HTTP requests to the API must be sent with an `Authorization` header (`Key`
  header is deprecated). See the
  [API documentation](https://hscc8udvc7gs.docs.apiary.io) for details.

There are ten (10) changes in total. Three (3) ✨changes✨ are worth more points
than the others:

## Change 1

**The Dashboard view now differentiates between answers that have been accepted
and answers that haven't.**

Before this change, the [Dashboard view](./qoverflow-part-1.md#requirement-5)
showed a user all the questions and answers they've created.

With this change, the Dashboard now shows all the questions a user has created,
all the answers they've created _that have not been accepted_, and all the
answers they've created _that have been accepted_. That is: it must be clear
which of the user's answers have been accepted and which have not.

All of the original display rules for answers still apply.

## Change 2

**`authed` users can now upvote, downvote, and delete comments.**

Before this change, it was not a requirement that users could upvote and
downvote comments, or delete their own comments.

With this change, "level 2" users can now upvote questions, answers, _and
comments_. Similarly, "level 4" ("level 5" after [change 4](#change-4)) users
can downvote questions, answers, _and comments_. Users below these levels cannot
upvote or downvote (respectively) comments.

Users cannot upvote or downvote their own comments. Unlike votes on questions
and answers, upvotes and downvotes on comments **do not** affect a user's total
number of points or their level. Users cannot upvote a comment they've already
downvoted and vice-versa. Users cannot vote on a comment they've already voted
on (e.g. upvoting multiple times is not allowed). These rules are all enforced
at the API level, so your team does not have to spend much time implementing
them.

Additionally, users can now delete their own comments.

## ✨Change 3✨

**In addition to points, `authed` users now receive "badges" for taking certain
actions within the system.**

Users can earn named badges—either gold, silver, or bronze—by taking any of the
following actions in the app:

> "Net total points" means "upvotes minus downvotes".

### Gold Badges

- **"Great Question"**: have a question with net total points of 100 or higher
- **"Great Answer"**: have an accepted answer with net total points of 100 or
  higher
- **"Socratic"**: have at least 10,000 points
- **"Zombie"**: have a question that is reopened

### Silver Badges

- **"Good Question"**: have a question with net total points of 25 or higher
- **"Good Answer"**: have an accepted answer with net total points of 25 or
  higher
- **"Inquisitive"**: have at least 3,000 points
- **"Protected"**: have a question that is protected

### Bronze Badges

- **"Nice Question"**: have a question with net total points of 10 or higher
- **"Nice Answer"**: have an accepted answer with net total points of 10 or
  higher
- **"Curious"**: have at least 100 points
- **"Scholar"**: accept an answer

Counts of the `authed` user's badges—grouped by gold, silver, and bronze—are
permanently visible within the app's navigation element along with other user
data. Additionally, when viewing their personal dashboard, users can see which
badges they've earned and which badges they haven't.

## ✨Change 4✨

**`authed` users can now add "bounties" to questions that do not have an
accepted answer.**

Bounties are a large amount of points that a user is willing to give to another
user whose answer becomes the accepted answer to a question. They're useful for
encouraging high quality answers to questions. Any user can add a bounty to any
other user's question, including their own.

Bounties cannot be created for less than 75 points or more than 500 points and
can only be added to questions that do not yet have accepted answers.
Immediately upon adding a bounty to a question, the adding user has the amount
of points deducted from their account's total points. A user cannot add a bounty
to a question that would leave them with less than 75 points in their account.

Once the creator of a question chooses to accept an answer, the bounty points
are added to the account of the user who created the accepted answer. Questions
can only have a single bounty added to them at a time. Bounties are permanent
and cannot be rescinded later.

Additionally, the app supports a new level for `authed` users:

- **Level 4** _(75 points)_ Add a bounty to a question without an accepted
  answer.

The old levels above "level 3" are bumped up by one, e.g. the old "level 4"
becomes "level 5," the old "level 5" becomes "level 6," etc. Including the new
"level 7" from [Change 5](#change-5), there are now nine (9) levels total.

## ✨Change 5✨

**`authed` users can now vote to edit questions and answers.**

Similar to
[Protection and Close/Reopen votes](qoverflow-part-1.md#protecting-questions-and-closingreopening-questions),
users of the appropriate level can now trigger and vote "yes" on editing a
question's title and/or body. Users of the appropriate level can also vote to
edit the body text of _answers_, too.

The user that initiates the edit vote must supply the new version of the
question/answer body text or the question's title, which you should store
locally. If and only if the edit vote succeeds, this stored text must replace
the old body text / title in [the API](https://hscc8udvc7gs.docs.apiary.io)
automatically.

Additionally, the app supports a new level for `authed` users:

- **Level 7** _(2,000 points)_ Trigger and participate in Edit votes on
  questions and answers.

The old levels above "level 6" are bumped up by one, e.g. the old "level 7"
becomes "level 8," the old "level 8" becomes "level 9," etc. Including the new
"level 4" from [Change 4](#change-4), there are now nine (9) levels total.

## Change 6

**Questions can now be "tagged".**

When a question is created, the creator can add "tags," which are single
alphanumeric words that ascribe additional meaning to the questions. Tags must
be handled in a case-insensitive manner. Each question can optionally have
between one (1) and five (5) tags added to it at creation time.

For example, a question about using `setTimeout()` in a React project in
JavaScript might be tagged with "javascript", "react", "timers", and "project".

A question's tags, if they exist, will appear in the
[Q&A](./qoverflow-part-1#requirement-3),
[Buffet](./qoverflow-part-1#requirement-2), and
[Dashboard](./qoverflow-part-1#requirement-5) views along with other metadata.

> Note that the API will not store tags for you. You'll have to store them
> locally and keep track of which questions are associated with which tags.

## Change 7

**Questions can be searched by tag.**

Questions can now be searched by tag in addition to the search functionality
required by the [original solution](./qoverflow-part-1.md#requirement-9).

> Note that the API will not store tags for you. You'll have to store them
> locally, keep track of which questions are associated with which tags, **and
> you'll have to implement tag searching yourself**.

## Change 8

**The Buffet view now shows when a question has an accepted answer.**

Each question listed in the [Buffet view](./qoverflow-part-1#requirement-2) will
differentiate in the UI between questions that have an accepted answer and
questions that do not.

## Change 9

**Mail is marked as "read" when opened and marked "unread" otherwise.**

When a user goes to view their mail messages, any mail they haven't yet looked
at should be marked "unread" in the UI. Mail that the user has already looked at
should be marked "read" in the UI. It is up to your team how to best communicate
to the user the difference between read and unread mail.

> Note that the API will not store a message's read/unread status for you. You
> will have to come up with a solution that leverages a local datastore.

## Change 10

**Questions and answers in the Q&A view can now be "shared".**

Questions and answers in the [Q&A view](./qoverflow-part-1#requirement-3) must
now include a "share" button/link that allows users to share a direct link to
the question or answer. Clicking the "share" button/link will
[copy into the user's clipboard](https://www.w3schools.com/howto/howto_js_copy_clipboard.asp)
a URL pointing to that question or answer.

For example, the URL might look like:

http://localhost:3000/your-app/question/unique-id-of-the-question-here

For sharing links to questions, this should be trivial. For sharing links to
answers, the "share" URL, when entered into the browser, will navigate the user
to the specific question then, depending on how you designed the Q&A view,
**automatically scroll down to the specific answer** or otherwise take the user
directly to the desired answer. This process can be instant and does not have to
be animated or fancy.

For example, the URL might look like:

http://localhost:3000/your-app/question/question-unique-id#answer-unique-id

For teams with solutions that lazily load answers, you may consider a pause to
wait for answers to load before scrolling down the viewport, or you may display
the linked-to answer at the top above all other answers (i.e. out of order). You
are free to implement this feature however you see fit so long as the
requirement is satisfied.
