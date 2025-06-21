
I am thinking about making a web project that helps Elon to build his Mars Colony. Elon already is buillding a big rocket, which is the most important thing. But to be able to build a second (sustainable) civilation on Mars the mars colonists need much more stuff.

I'd like to make a full features web app that is requirement based. In a hierarchical navigation users can suggest requirements for items required for building the base on mars. Then users can add different types of items (e.g. goods, community projects, etc.) that fulfill the requirement.

- use typical data fields for each item type, but use ony the most important for now for simplicity
- also include availability date
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
  /items of type "good", "community-project"

    id
    name
    description:         Short description
    projectLead:         User ID of project lead
    primaryImage
    images:              Array of image URLs

    relatedRequirements: Array of requirement IDs this item fulfills
    dependencies:        personnel/goods needed
    tags:                Array of category tags

    userRating
    availabilityDate:    Expected availability (percentage or specific date)

    mass:                Mass in kg
    volume:              Volume in cubic meters
    shape
    powerRequirement:    Power needed in watts
    maintenance:         maintenance requirements (time, duration)
    lifespan:            Expected lifespan

    fundingGoal:         Amount of funding needed
    currentFunding:      Current amount raised
    contributors:        Array of user IDs who contribute
    volunteerRoles:      Types of volunteers needed

    createdBy:           User ID of creator
    createdAt:           Creation timestamp
```

fields
nav
