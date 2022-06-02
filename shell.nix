# simple environment for running / debugging tests using [Nix](https://nixos.org)
# to use this file:
# 1. install Nix: `sh <(curl -L https://nixos.org/nix/install) --no-daemon`
# 2. Run command: `nix-shell`

{ pkgs ? import <nixpkgs> {}}:

let
    myPhp = pkgs.php74.buildEnv {
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
      vendor/bin/phpunit --no-coverage --exclude-group=infra,needs-review
    '';
}
