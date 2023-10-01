Select users.id,
users.email,
info.first_name,
info.last_name,
info.type,
info.is_active,
s.id as 'Store ID',
s.name as 'Store Name',
concat(s.domain, '.sales-tracker.local:8880') as 'domain',
g.name as 'Team',
r.name as 'Role'

from users
left join user_stores us on us.user_id = users.id
left join user_information info on info.user_id = users.id
left join stores s on s.id = us.store_id
left join group_team_members gts on gts.user_id = users.id
left join group_teams g on g.id = gts.`group_teams_id`
left join role_user ru on ru.user_id = users.id
left join roles r on r.id = ru.role_id;
