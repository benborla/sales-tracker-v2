[May 12, 2023]
- Added dependency pktharindu/nova-permissions
  - documentation: https://novapackages.com/packages/pktharindu/nova-permissions
  - Issue found:
    - NovaRequest is still being used, temporary fixed is to replace it with
      "Illuminate\Http\Request as NovaRequest"
    - Vue is required in NovaPermission, need to install this
  - Removed package [May 13, 2023]

[May 13, 2023]
- Added depedency of Roles and Permissions: composer require silvanite/novatoolpermissions
  - Documentation: https://novapackages.com/packages/silvanite/novatoolpermissions
  - Updates:
    - Role is now working
  Issues:
    - [FIXED] `withStore` is not working, it's fetching the first user in the records
      - Added user id in the function

[May 17, 2023]
- Add `store_id` on roles

[May 19, 2023]
- [NEW] Roles now loads a specific roles only for that store, if the main sales tracker
  is accessed, it will load the entire role from the database

[May 25, 2023]
- [NEW] User Information model and resource

[May 30, 2023]
- [NEW] Added User Information resource

[May 31, 2023]
- [NEW] Attached User Information to User resource

[June 8, 2023]
- [NEW] Added User store resource
- [NEW] Added store resource
- Added logic to retrieve available stores for the user.
- Added new dependency "SelectPlus"
- Add logic to only display User Stores resource if the user has the SALES_TRACKER_ADMIN role

[June 22, 2023]
  - Finished Store resource

[July 18, 2023]
  - Added Order Entry table and model
  - Added Group and Group Team resource

[July 25, 2023]
 - Added rule validation on Team, it won't allow if the user is already existing
 in the selected team

[July 31, 2023]
 - Fixed issue with regards to adding multiple teams to a user,
 - Added "DuplicateUserInGroup" rule that will check if the user in a team already
 exists.
 - Added product resource in Nova


