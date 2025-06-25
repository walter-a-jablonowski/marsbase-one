
I am thinking about making a web project that helps Elon to build his Mars Colony. Elon already is building a big rocket, which is the most important thing. But to be able to build a second (sustainable) civilization on Mars the mars colonists need much more stuff.

I'd like to make a full-featured web app that is requirement based. In a hierarchical navigation users can suggest requirements for items required for building the base on mars. Then users can add solutions ("items") that fulfill the requirement.

Pages:

- Start
  - Texts
  - Top Requirements
  - Top Solutions
- Login
- Register
- Requirements
  - requirements start page: Main list of base requirement (use cards)
    - main requirement list initially is sorted by a sort order defned in config (add one)
    - each entry (choose a nice layout):
      - Up down arrow for user score, each user has one vote (users may change their choice)
      - show the most important data fields for the requirement
      - image (on the right side)
      - buttons for all functions
  - click on an requirement to navigate to the requirement page
  - requirement page:
    - breadcrumb
    - requirement name
    - all information of this requirement (read only)
    - buttons for all functions, for edit use modal
    - tab control:
      - list on each tab initially sorted by user score descending
      - first tab: "Sub Requirements"
        - basic filter and sorting controls
        - suggest solution button (for edit use modal)
        - list of sub requirements
      - second tab: "Solutions" (the items)
        - basic filter and sorting controls
        - suggest solution button (for edit use modal)
        - list of items
- People: show your expertise for mars
  - basic filter and sorting controls, including show people or organisation only and filter by location
    - add criteria that make sense
  - list of organisations and people (mixed)
    - show the most important data fields for the person
    - include show user profile page
- we use no separete items page
- Profile (user personal profile page)
  - persons may be part of one or more organisations
  - followed/favorite requirements, items and users
- Settings (currently empty)
- Logout

All users can view all requirements and solutions without login. Only logged in users can edit (see user ids defined in the data).

Items of type "project" (community projects) may ask for funding, which we implement as a dummy UI for now. We will add a payment feature later.

Make the web app full features including login system and basic error display in case of app errors. Make it based on bootstrap 5.3 but alter the design using styles so that it matches "mars colony" theme of the app. Make the UI responsive and work on all devices.

We use config.yml, simple PHP code with simple classes and put all data in the /data folder (no database).

We use speaking File names for all files in data folders:

- derive it from the field "name" (for users: name and location)
- convert each word to first character uppercase
- then remove all non alpha numeric chars
- add a short random string to the end

Data files:

```
/data
  sequences.json
  /users

    ```
    id:                   numeric (use a json file sequences.json that has the last used ids)
    type:                 "person" or "organization"
    email:                email (unique)
    password:             Hashed password
    name:                 Full name or organization name
    bio:                  Short biography or description
    expertise:            Areas of expertise/skills
    image:                uploaded file jpg, jpeg, png (gets some hash as file name on upload)
    location:             Physical location (Earth city/country or Mars base)

    memberIds:            if person: array of org ids, if org: array member ids
    followedItems:        Array of item IDs
    followedReq:          Array of requirement IDs
    followedUsers:        Array of user IDs
    itemScore:            Array of items up- or downvoted like [{id: ID, score: 1|-1}, ...]
    reqScore:             Array of requirements up- or downvoted

    modifiedAt:           YYYY-MM-DD HH:MM:SS
    ```

  /requirements (hierarchical)
    /SomeRequirement_RND_STRING
      /uploads
      /requirements = sub requirements

      data.yml:

        ```
        id                    numeric
        parentIds
        childIds:             IDs of child requirements (for hierarchy), requirements can have multiple parents (e.g. power supply needed for different things)
        relatedIds:           Array of related requirement IDs (e.g. dependencies)
        userIds:              Array of user IDs that may edit this requirement
        name
        status:               "proposed", "validated"
        description:          Short description
        detailed:             Longer explanation
        primaryImage:         uploaded file (gets some hash as name on upload)
        images:               Array of image hashes

        itemIds:              Array of item IDs that fulfill this requirement

        score:                Calculated from up/downvotes

        createdBy:            User ID of creator
        modifiedAt:           YYYY-MM-DD HH:MM:SS
        ```

      /solutions (only the creator may edit a solution item)
        /SomeSolution_RND_STRING
          /uploads

          data.yml:

          ```
          id                   numeric
          type:                "item", "service" or "project"
          requirementIds:      Array of requirement IDs that this item fulfills
          name
          description:         Short description
          projectLead:         User ID of project lead
          availabilityDate:    Expected availability (percentage or specific date)
          primaryImage:        uploaded file (gets some hash as name on upload)
          images:              Array of image hashes

          score:               Calculated from up/downvotes

          mass:                Mass in kg
          volume:              Volume in cubic meters
          shape

          fundingGoal:         Amount of funding needed
          contributions:       Array of user who contribute like [{user: USER_ID, time: YYYY-MM-DD HH:MM:SS, amount: AMOUNT}, ...]
          currentFunding:      Calculated from contributions
          volunteerRoles:      Types of volunteers needed

          createdBy:           User ID of creator
          modifiedAt:          YYYY-MM-DD HH:MM:SS
          ```
```
