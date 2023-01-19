<?php

///////////////////////
// ADMIN COLUMNS PRO //
///////////////////////

use AC\ListScreenRepository\Storage\ListScreenRepositoryFactory;
use AC\ListScreenRepository\Rules;
use AC\ListScreenRepository\Rule;
add_filter( 'acp/storage/repositories', function( array $repositories, ListScreenRepositoryFactory $factory ) {
    
    //! Change $writable to true to allow changes to columns for the content types below
    $writable = true;
    
    // 2. Add rules to target individual list tables.
    // Defaults to Rules::MATCH_ANY added here for clarity, other option is Rules::MATCH_ALL
    $rules = new Rules( Rules::MATCH_ANY );
    $rules->add_rule( new Rule\EqualType( 'attractions' ) );
    
    // 3. Register your repository to the stack
    $repositories['attractions'] = $factory->create(
        NEIGHBORHOOD_ATTRACTIONS_DIR . '/inc/acp-settings',
        $writable,
        $rules
    );
    
    return $repositories;
    
}, 10, 2 );