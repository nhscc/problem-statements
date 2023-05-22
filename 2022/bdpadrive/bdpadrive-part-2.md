# BDPA NHSCC 2022 Problem Statement (part 2)

> See also: [API documentation](https://hscchkie87hj.docs.apiary.io)

BCS applauds the successful rollout of its new word processing app! Feedback
indicates users are satisfied with your app UX and performance data shows
response time tail latencies are very low. But your contractor has identified
some changes they want implemented.

> We're looking for feedback! If you have any opinions or ideas, contact us on
> Slack.

🚧 🚧 To avoid disqualification, please take note of the following:

- Your solution’s landing page must be available at `http://127.0.0.1:3000`
  (localhost)
- Your solution’s source code must be located at `%USERPROFILE%\Desktop\source`
- HTTP requests to the API must be sent with an `Authorization` header (`Key`
  header is deprecated). See the
  [API documentation](https://hscchkie87hj.docs.apiary.io) for details.

There are ten (10) changes in total. Three (3) ✨changes✨ are worth more points
than the others:

## Change 1

**Your application must be updated to use the new V2 API exclusively.**

See
[the documentation](https://hscchkie87hj.docs.apiary.io/#/introduction/migration-guide)
for the new version of the API. You will need to update the base address you're
using from https://drive.api.hscc.bdpa.org/v1 to
**https://drive.api.hscc.bdpa.org/v2**. The documentation also includes a brief
migration guide.

> We will disable the v1 API when testing your solution. If your solution
> malfunctions afterwards, you will lose a significant amount of points.

## ✨Change 2✨

**`authed` users can now share files and folders with one another.**

Nodes (files and folders) can now have permissions set on them indicating that a
node has been shared with another user.

This is done via the
[`permissions`](https://hscchkie87hj.docs.apiary.io/#/data-structures/0/file-node)
property. The value of this property is an object that is a mapping between
usernames and user permissions. For example, to show that a file named
"yellow-flicker-beat" owned by User1 can be viewed by User2 and User3 and edited
(which implies the ability to view also) by User4, the object's `permissions`
property would look like the following:

```json
{
    "node_id": "5ec8adf06e38137ff2e58770",
    "owner": "User1",
    "createdAt": 1579489874164,
    "modifiedAt": 1579164489874,
    "type": "file",
    "name": "yellow-flicker-beat",
    "size": 44,
    "text": "My necklace is a rope I tie it and untie it.",
    "tags": ["lorde", "music"],
    "lock": null,
    "permissions": {
      "User2": "view",
      "User3": "view",
      "User4": "edit"
    }
},
```

The V2 API takes permissions into account during searches and other GET
operations by returning nodes that are owned by various users so long as the
user performing the operation either owns the node or is listed in the
`permissions` object for the node.

You can also search by permissions via `match`. See the
[API search documentation](https://hscchkie87hj.docs.apiary.io/#/reference/0/user-endpoints/users-username-filesystem-search-get)
for an example.

Users that have the `"view"` permission on a file can only open the node
(rendering its text as Markdown and/or viewing its contents) but cannot modify
it (via PUT operations). Users that have the `"edit"` permission can both open
files/folders _and_ modify their text/content, even if they are owned by a
different user.

Only the owner can add, change, or remove permissions on a node, rename the
node, move the node, or delete the node. The one exception is with "unsharing":
users can unshare files and folders that have been shared with them, even if
they are not the owner of these nodes.

## Change 3

**Symlinks can now point to files that the user does not own.**

Before this change, symlinks that pointed to files and folders that a user did
not own were considered "broken" and were non-functional in the app. After this
change, the user can now use symlinks that point to files that they do not own
so long as both the symlink and the file the symlink is pointing to have the
necessary permissions.

Symlink permissions **cannot** override the permissions of the node they point
to. That is: if a symlink has "edit" permissions for User1 but the file the
symlink is pointing to only has "view" permissions for User1, then User1 can
only view the file. If a symlink has "view" permissions for User1 but the file
the symlink is pointing to has _no_ permissions for User1, then the symlink is
broken.

Beyond that, use your best judgement to determine how symlink sharing and
permissions must work in your app.

## Change 4

**Users can now make their files available to the public.**

The special
[`permissions`](https://hscchkie87hj.docs.apiary.io/#/data-structures/0/file-node)
property "public" can be used to indicate that a file must be visible to _all_
users, including unauthed `guest` users. The special "public" property can only
be set to "view".

When viewing files with the "public" permission set, the
[Editor view](./bdpadrive-part-1.md#requirement-3) can be used without
authenticating first. For example:

```json
{
    ...
    "permissions": {
      "User2": "view",
      "User3": "view",
      "User4": "edit",
      "public": "view"
    }
},
```

Only text files can be made public. Folders and symlink files cannot be made
public.

## ✨Change 5✨

**Users can now share public text files across the internet via URL.**

That is: a restful URL like
http://localhost:3000/your-app/editor/unique-id-of-public-file-here will lead
anyone who clicks on the link directly to the shared public file without forcing
them to authenticate first. Attempting this with a file that has not been made
public, however, will fail.

## Change 6

**Deleting a user's account no longer deletes _all_ of their files.**

Before this change, when a user chose to delete their account, all of the files
and folders they owned were also deleted by your app. After this change, when a
user chooses to delete their account, all of the files and folders that they own
must also be deleted by your app **except those files and folders that the user
has shared with other users**. On the other hand, files and folders that _only_
have public permissions set must still be deleted.

## Change 7

**Your locking mechanic must now take into account that users can "lock" each
other's files.**

Before this change, your app only had to worry about locking file edits between
[different clients](./bdpadrive-part-1.md#requirement-6) of the same user. Now
that users can share their files with one another, and grant each other edit
permissions on those shared files, your mechanic must now account for locking
edits between different clients _and_ different users if it does not already.

That is: locks can now be owned by different user-client combinations where both
the user and the client might be different where before, back when users could
only edit files they already owned, only the client would have differed.

## Change 8

**New Shared view: `authed` users can see files and folders they're sharing and
that have been shared with them.**

This view is a special version of the
[Explorer view](./bdpadrive-part-1.md#requirement-2), except it shows only two
types of nodes: nodes that the user is sharing with other users and nodes that
other users are sharing with this user.

The two categories of nodes will be visually separated in the UI and it must be
obvious which are which.

From this view, a user will be able to easily "unshare" nodes they are sharing
with others or that have been shared with them.

## ✨Change 9✨

**New Recycle Bin view: deleting a file puts it into a "Recycle Bin" folder
instead of deleting it immediately.**

Before this change, deleting a node in the UI was permanent. After this change,
when deleting a node, that file or folder is instead moved into a special
"Recycle Bin" folder. This is referred to as a "soft delete".

Users can access the contents of this special folder from a new Recycle Bin
view. Here, a user can choose to permanently delete ("hard delete") any
files/folders that are in the bin or to move them out of the bin ("restore"
them).

If the "Recycle Bin" folder doesn't exist when a soft delete happens, it will be
(re-)created automatically by your app. It is up to you if the Recycle Bin
folder is visible/accessible from the normal
[Explorer view](./bdpadrive-part-1.md#requirement-2).

It must not be possible to share or otherwise set permissions on the Recycle Bin
folder. This must be enforced at the application layer; the API will not enforce
this for you.

When a user [deletes their account](#change-6), all node deletions must bypass
the "Recycle Bin" folder. In fact, the "Recycle Bin" folder itself, and all of
its contents, must also be deleted.

## Change 10

**New toggleable website theme: dark mode.**

> Or, if your app UI already uses light text on a dark background, it needs a
> _light mode_!

The users have spoken and they want the ability to
[toggle the website to a dark version of itself](https://codepen.io/adhuham/pen/GRJxpQr),
dubbed _dark mode_. While the UI normally features blackish text on a bright
background, when in dark mode the UI will render white text on a darkish
background.
