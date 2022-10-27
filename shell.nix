# simple environment for running / debugging tests using [Nix](https://nixos.org)
# to use this file:
# 1. install Nix: `sh <(curl -L https://nixos.org/nix/install) --no-daemon`
# 2. Run command: `nix-shell`

{ pkgs ? import <nixpkgs> {}}:

let
    myPhp = pkgs.php81.buildEnv {
        extensions = ({ enabled, all }: enabled ++ [ all.xdebug] ++ [ all.apcu ]);
        extraConfig = ''
         memory_limit=256M
        '';
    };
in
pkgs.mkShell {
    packages = [
        myPhp
        myPhp.packages.composer
        myPhp.packages.psysh
        pkgs.nginx
    ];

    shellHook = ''
      export XDEBUG_MODE=coverage
      vendor/bin/phpunit
    '';
}
