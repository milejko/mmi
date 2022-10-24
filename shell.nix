# simple environment for running / debugging tests using [Nix](https://nixos.org)
# to use this file:
# 1. install Nix: `sh <(curl -L https://nixos.org/nix/install) --no-daemon`
# 2. Run command: `nix-shell`

{ pkgs ? import <nixpkgs> {}}:

let
    myPhp = pkgs.php81.buildEnv {
        extensions = ({ enabled, all }: enabled ++ [ all.xdebug] ++ [ all.apcu ]);
        extraConfig = ''
         xdebug.mode=debug
        '';
    };
in
pkgs.mkShell {
    packages = [
        myPhp
        myPhp.packages.composer
    ];

    shellHook = ''
      export APP_DEBUG_ENABLED=1
      export APP_BASE_URL=
      export CACHE_SYSTEM_ENABLED=0
      export CACHE_PUBLIC_ENABLED=0
      export CACHE_PUBLIC_HANDLER=file

      export DB_HOST=localhost
      export DB_USER=3306
      export DB_NAME=test
      export DB_PASSWORD=test

      vendor/bin/phpunit --no-coverage --exclude-group=infra,needs-review
      vendor/bin/phpunit -c phpunit.app.xml --testdox
    '';
}
