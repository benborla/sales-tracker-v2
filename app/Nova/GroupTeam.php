<?php

namespace App\Nova;

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\TextArea;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Select;

class GroupTeam extends Resource
{
    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'Administration';

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\GroupTeam::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
        'name',
        'notes'
    ];

    public static function label(): string
    {
        return 'Teams';
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        $teamLeads = User::getTeamLeads()
            ->get()
            ->mapWithKeys(function ($user) {
                return [$user->user_id => "$user->last_name, $user->first_name $user->middle_name ($user->email)"];
            })->toArray();

        return [
            ID::make(__('ID'), 'id')->sortable(),
            Select::make('Team Leader', 'team_lead_user_id')
                ->withMeta(['data-field' => 'user-field'])
                ->required()
                ->options($teamLeads)
                ->searchable()
                ->onlyOnForms(),

            BelongsTo::make('Store')->display('name'),
            Text::make('Name', 'name')->sortable(),
            TextArea::make('Notes', 'notes')->sortable(),
            HasMany::make('Members', 'members', \App\Nova\GroupTeamMember::class)->sortable(),
            DateTime::make('Created At')->sortable()->onlyOnIndex(),
            DateTime::make('Updated At')->sortable()->onlyOnIndex(),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
