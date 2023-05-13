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
    - `withStore` is not working, it's fetching the first user in the records

