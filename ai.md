
I am tinking about making a web project that helps Elon to build his Mars Colony. Elon already is buillding a big rocket, which is the most important thing. But to be able to build a second (sustainable) civilation on Mars the mars colonists need much more stuff.

I'd like to make a full features web app that is requirement based. In a hierarchical navigation users can suggest requirements for items required for building the base on mars. Then users can add different types of items (e.g. goods, services, community projects, etc.) that fulfill the requirement.

- use typical data fields for each item type, but use ony the most important for now for simplicity
- also include a readiness level (TRL) and a availability date (e.g. 10%)
- requirement also have data fields e.g. a list of related items (e.g. power → habitat)

For the requirements as well as the items users can Up- or downvode (users may change) which is used for sorting. Also add basic filter and search functions.

Community projects can ask for funding, which we implement as a dummy for now. We will add a payment feature later.

Users can be persons or organizations. Persons may be part of an Organisation. Users have a personal profile page and they can:

- Comment
- Follow/favorite requirements and items

Make the web app full features including login system. Make it based on bootstrap 5.3 but alter the design using styles so that it matches "mars colony" theme of the app. Make the UI responsive and work on all devices.

We use PHP with simple classes and put all data in the /data folder (no database):

```
/data
  /users of type "organization" or "person" (person may have orgId)
  /requirements (hierarchical)
  /items of type "good", "service", "community-project"
```
