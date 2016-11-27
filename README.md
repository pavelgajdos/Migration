# Migration

This library is a simple database migration tool for Nette. It runs SQL commands from files which are stored within your application. 

The only setup is to specify a directory in which migration files are stored. This can be done in Nette configuration file. I suggest using a template that is a part of this library (config/extension.pg.migration.neon).

It uses Joseki/Console (based on Symfony/Console) to run from the command line. 

**Commands**
- bin/migration create [name] - Creates a new migration file in the migration directory. It defaults to 'migration'.
- bin/migration migrate - Runs SQL commands from files that haven't been processed yet.


In case you don't want to use Joseki/Console, register MigrationManager on your own (the constructor requires as the first parameter a path to migration directory) and use its public methods accordingly.


## Migration directory

Files within this directory match a naming pattern: SEQID_name[\_OK].sql

Files that ends with '\_OK.sql' have been processed. 


