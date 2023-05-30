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

