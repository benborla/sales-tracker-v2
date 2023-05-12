[May 12, 2023]
- Added dependency pktharindu/nova-permissions
  - documentation: https://novapackages.com/packages/pktharindu/nova-permissions
  - Issue found:
    - NovaRequest is still being used, temporary fixed is to replace it with
      "Illuminate\Http\Request as NovaRequest"
    - Vue is required in NovaPermission, need to install this
