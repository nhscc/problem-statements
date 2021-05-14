# BDPA NHSCC 2020 Problem Statement (part 2)

BDPA Airports, Inc., the entity that contracted you to build their local airport
system, applauds its successful rollout! User feedback indicates users are
satisfied with your app UX and performance data shows response time tail
latencies are very low. But your contractor has identified some changes they
want to make to the original app specification.

> Please [let us know](https://github.com/nhscc/problem-statements/issues) if
> you have any questions or concerns.

## Change 1

**Customer accounts can now be _banned_ or _un-banned_ by admins; flyers can
also be prevented from booking with specific airlines.**

A _banned_ customer is not allowed to login. An _un-banned_ customer is allowed
to login. Only admins can un/ban customers.

By default, new customers will be un-banned. Banning and un-banning a customer
will not alter their flight bookings. If a customer is logged in and their
account becomes banned, **they will be forced to log out when they next interact
with the app.**

When a customer is banned, no flights can be booked using any of the
credit/debit cards they have on file, even if those cards are being used by a
guest or another customer.

Instead of banning a customer, admins can also restrict from which airlines a
customer can purchase a ticket without banning that customer's account.

## Change 2

**The system will support an additional user type: airline attendants.**

Along with guests, customers, and admins, there is now a 4th required user type:
attendants. Admins create them and assign them to an airline. Attendants are
somewhat similar to admins in that they can:

- See a paginated list of all customer and ticket information for any flight
  belonging to their assigned airline
  - They can view related customer information but cannot modify it
- Create new tickets for flights belonging to their airline
- View a ticket for flights belonging to their airline using its confirmation
  code
- Cancel tickets for upcoming flights belonging to their airline
  - Cancelling a ticket will deactivate it in the system and allow the seat to
    be purchased by someone else. The ticket will show up on the
    dashboard/ticket as "canceled" or "refunded" and the ticket will otherwise
    not be accessible.
  - This is irreversible, though the user could always buy another ticket.
- Search for a customer with their assigned airline using any user information
  (email, address, name, etc)
- Search for a ticket to a flight belonging to their assigned airline using any
  flight information (departing airport, arrival airport, etc)

They should probably have their own dashboard.

## Change 3

**The system will now include a check-in view.**

After booking a flight, instead of immediately giving the customer their ticket,
they will be told their purchase was successful and they will be given their
airline confirmation number. Customers will be able to navigate to a check-in
view that accepts their last name and airline confirmation number and then:

- If the flight departs within the next 24 hours, the customer is allowed to
  check in or cancel the ticket
  - Checking in means:
    - The customer can no longer check-in (and the UI will reflect this)
    - The customer can now access their ticket view for the flight as normal
- If the flight does not depart within the next 24 hours, the customer is
  instructed to try again later

Customers, when accessing their dashboard view and looking at upcoming flights,
will see a "check-in" button or something similar next to flights that are
departing within the next 24 hours. This link will lead directly to the check-in
view, bypassing the part where last name and airline confirmation number are
usually required.

If a customer attempts to check-in to a flight they already checked-in to, they
will be redirected to the ticket view to see their ticket for this flight. If a
customer attempts to access the ticket view of their flight without first
checking in, they will be redirected to the check-in view.

## Change 4

**Your application must be updated to use the new V2 API exclusively.**

See the documentation for the new version of the API
[here](https://hscc210ff8c0.docs.apiary.io). You will need to update the base
address you're using from https://airports.api.hscc.bdpa.org/v1 to
**https://airports.api.hscc.bdpa.org/v2**. The documentation also includes a
brief migration guide.

> We will disable the v1 API when testing your solution. If your solution
> malfunctions afterwards, you will lose a significant amount of points.

## Change 5

**Customers’ dashboard views will track "frequent flier miles" (FFMs). Customers
can purchase tickets with FFMs or money.**

Using the V2 API, each flight data request now responds with how many FFMs the
flight will credit to customers who purchased a ticket. Customers will then be
able to use their stored FFMs as an option to purchase tickets instead of money
when using the booking view. Customer dashboards will display how many FFMs the
customer has. **When a customer purchases a ticket using their FFMs, they do not
receive FFMs for that flight.**

When cancelling a ticket, any FFMs the customer got for purchasing the ticket
will be deducted from their account; any FFMs the customer spent to purchase
their ticket or in-flight extras will be refunded.

Guests cannot use FFMs.

## Change 6

**In the flight booking view, users can select from a class of seats and
in-flight extras can now be purchased.**

[The new V2 API](https://hscc210ff8c0.docs.apiary.io) now includes seating class
data. This data will be used to indicate the four different seat classes in the
UI:

- Economy (default)
- Exit row
- Economy plus
- First class

Each seat class will have a price associated with it, meaning all seats will not
have the same price anymore. **This means the prices of flights are no longer
constant per-flight like the previous version of the API, but depend on the
selected seat.** Also, flights are no longer limited to 90 seats.

The API also includes data on a new feature: in-flight extras like wifi are now
available for purchase. These can also be bought using either money or FFMs.
Users are free to mix and match their purchases, choosing to pay with ffms for
some items and dollars for others. See
[the API documentation](https://hscc210ff8c0.docs.apiary.io) for how to
determine which flights have which extras and at what cost.

> Note that price data includes dollar amounts _and cost in FFMs_ since users
> can use either to purchase tickets and extras.

## Change 7

**All views include a persistent sidebar showing customers their upcoming flight
and other information.**

On some side of the page (or fixed to the browser window), the customer will be
able to see some information about their nearest upcoming flight, as well as
their email address and other useful account information. Specifically:

- Flight airline and number
- Destination
- Departure date and time
  - If this is updated by the API, it will be updated in the view asynchronously
- First name (e.g. "Hi, yourname!")
- Email address

## Change 8

**Users must book flights at least 36 hours before they are scheduled to take
off.**

Users must now book flights at least 36 hours before the flight’s departure
time. The "book this flight" button(s) on the public flight information view
will be updated to appear only if the flight is an upcoming flight scheduled to
depart in more than 36-hours.

## Change 9

**Baggage fees and restriction information must be taken from the V2 API.**

Before, carry-on and checked baggage prices were fixed. Now, the price for
carry-ons and checked baggage will be taken from the new V2 API. See
[the API documentation](https://hscc210ff8c0.docs.apiary.io) for details.

Similarly, the number of allowed carry-on and checked bags is no longer fixed
and must be acquired from the API.

## Change 10

**Switch to email-based logins.**

Management has determined that the strange name-based login system is too
cumbersome for users. Instead, change it so users can login using their email
address and password instead of their name and whatever else. This should work
just fine since email addresses are already required to be unique.
