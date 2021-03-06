# Beta 6 Upgrade Guide
## Schema changes
These changes affect you if you are using the schema automatically generated by the `graphql_core` module.


### Language handling

Multilingual queries changed drastically. The endpoints language negotiation is ignored entirely, instead all language handling is left to the query and its arguments. A couple of fields accept a "language" argument, and whenever this argument is filled explicitly, it's value will be inherited to subsequent occurrences. The `route` field will set this context implicitly from the paths language prefix.

```graphql
query {
  route(path: "/node/1") {
    ... on EntityCanonicalUrl {
      entity {
        # Will emit the default language.
        entityLabel
      }
    }
  }
  route(path: "/fr/node/1") {
    ... on EntityCanonicalUrl {
      entity {
        # Will emit the french translation.
        entityLabel
      }
    }
  }
}

```

### Url Interfaces
The type structure of the `Url` object changed. While before there have been just the `InternalUrl` and `ExternalUrl` types, the `InternalUrl` has become an interface that can resolve to different Url types, depending on the underlying route. The `DefaultInternalUrl` has the fields for context resolving and other generic rout information. The `EntityCanonicalUrl` has access to the underlying entity.

In a nutshell, if you were using this kind of query:

```gql
query {
  route(path: "/node/1") {
    entity {
      entityLabel
    }
  }
}
```

It has to become this:
```gql
query {
  route(path: "/node/1") {
    ... on EntityCanonicalUrl {
      entity {
        entityLabel
      }
    }
  }
}
```

### Entity Interfaces
The interfaces for entities have been aligned with the entity interfaces in Drupal. There is an `EntityOwnable`, `EntityPublishable` and `EntityRevisionable` interface which now hold the respective fields.

Before:
```gql
query {
  entity:route(path: "/node/1") {
    ... on EntityCanonicalUrl {
      entity {
        entityOwner {
          uid
        }
        entityPublished
        entityRevisions {
          count
        }
      }
    }
  }
}

```

After:

```gql
query {
  entity:route(path: "/node/1") {
    ... on EntityCanonicalUrl {
      entity {
        ... on EntityOwnable {
          entityOwner {
            uid
          }
        }
        ... on EntityPublishable {
          entityPublished
        }
        ... on EntityRevisionable {
          entityRevisions {
            count
          }
        }
      }
    }
  }
}

```

### Uppercased Enums
Emumerations are uppercased now. For the schema generated by `graphql_core` this means language identifiers and image styles.

Before:

```gql
query {
  entity:route(path: "/node/1") {
    ... on EntityCanonicalUrl {
      entity {
        entityTranslation(language: de) {
	  ... on NodeArticle {
            entityLabel
            fieldImage {
              derivative(style: thumbnail) {
                url
              }
            }
  	  }
        }
      }
    }
  }
}
```

After:
```gql
query {
  entity:route(path: "/node/1") {
    ... on EntityCanonicalUrl {
      entity {
        entityTranslation(language: DE) {
	  ... on NodeArticle {
            entityLabel
            fieldImage {
              derivative(style: THUMBNAIL) {
                url
              }
            }
       	  }
        }
      }
    }
  }
}
```

### Optimized simple fields
Entity fields that contain only one property are simplified and directly emit that value.

Before:
```gql
... on NodeArticle {
  fieldPlainText {
    value
  }
}
```

After:
```gql
... on NodeArticle {
  fieldPlainText
}
```

### Using persisted queries

If you have been using persisted queries, your clients where calling the endpoint url and passing separate `id` and `version` arguments.

```
/graphql?id=myquery&version=123
```

These have been merged into one `queryId` argument.

```
/graphql?queryId=123:myquery
```

### Null in string results
Due to the way the new execution library handles results, `null` values in Strings, an empty string field will not yield `null` but a string containing `???null???`.

## API changes
These changes affect you if you???ve been developing custom schema plugins.

### Development flag
The three settings for result cache, schema cache and field security have been replaced with one `development` flag, which turns the GraphQL module into development mode. This will also enable advanced error reporting in the underlying execution library.

Before:
```yaml
parameters:
  graphql.config:
    # GraphQL result cache:
    #
    # By default, the GraphQL results get cached. This can be disabled during development.
    #
    # @default true
    result_cache: true

    # GraphQL schema cache:
    #
    # By default, the GraphQL schema gets cached. This can be disabled during development.
    #
    # @default true
    schema_cache: true

    # Development mode:
    #
    # Disables field security. All fields can be resolved without restrictions.
    #
    # @default false
    development: false
```

After:
```yaml
parameters:
  graphql.config:
    # Development mode:
    #
    # Enables debugging mode and disables field security and caching.
    #
    # When enabled, all fields can be resolved without restrictions
    # and the caching strategy of the schema and query results is
    # disabled entirely.
    #
    # @default false
    development: false
```

### Enum definitions
`buildEnumValues` has to return an array of the following structure:

```php
[
  'VALUE_A' => [
    'value' => 'a',
    'description' => 'The letter a',
  ],
]
```

Instead of:
```php
[
  [
    'value' => 'a',
    'name' => 'VALUE_A',
    'description' => 'The letter a',
  ],
]
```

By convention the enums keys should be uppercased.

### Resolve context
The field plugins resolve methods receive an additional argument of type `ResolveContext`, that can be used to pass information to children down the query tree.

If your field plugins have strict method annotations like this:
```php
public function resolveValues($value, $args, ResolveInfo $info) {
...
}
```

They have to be changed to:
```php
public function resolveValues($value, $args, ResolveContext $context, ResolveInfo $info) {
...
}
```

### Mutations: extractEntityInput
The mutation plugins resolve methods receive an additional argument of type `ResolveContext` and `$value`, that can be used to pass information to children down the query tree.

If your field plugins have strict method annotations and a signature that accepts two parameters like this:
```php
public function extractEntityInput(array $args, ResolveInfo $info) {
...
}
```

They have to be changed to:
```php
public function extractEntityInput($value, array $args, ResolveContext $context, ResolveInfo $info) {
...
}
```

Change/Amend the use statements for those classes like so:
```
use GraphQL\Type\Definition\ResolveInfo;
use Drupal\graphql\GraphQL\Execution\ResolveContext;
```

### Processors and persisted Queries
The interfaces for the `QueryProcessor` and `QueryProvider` changed slightly. The `QueryProcessor` doesn???t accept a pair of `$query` and `$variables` any more, but an object of type `OperationParams`. The `QueryProviders` `$id` and `$version` arguments have been merged into one `$id` argument that has to contain both.
