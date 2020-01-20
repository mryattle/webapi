<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\Models\Movie; 
use App\Models\MovieRank; 
// Validation
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\MessageBag;

class RankMovie
{
    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  mixed[]  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    public function __invoke($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        // TODO implement the resolver
        $movie = Movie::findByUnique($args["movie"]["identifier"]);
        $movie_rank = MovieRank::findByMovieAndVisitor(
            $args["movie"]["identifier"],
            \Request::ip());
        if(is_null($movie_rank))
            // Writing in transactional enviroment
            $movie_rank = new MovieRank($args);
        else
            $movie_rank->fill($args); 
        $entity = Movie::findByUnique($args["movie"]["identifier"]);
        $movie_rank->movie()->associate($entity);
        $movie_rank->save();
        return $movie;
    }
}
