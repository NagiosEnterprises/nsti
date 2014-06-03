#!/bin/sh

# Checks if NSTI has been installed
is_installed () {
    for file in $@;
    do
        if ! rpm -qa "$file" 2> /dev/null;
        then
            echo "$file is not installed. You need to install using your distro's package manager."
            return 1
        fi
    done
    return 0
}

BASEPATH=$(dirname `readlink -f $0`)

# Adds specified user if it doesn't exist already
add_user() {
	local user
	user="$1"

	if ! grep -q "^$user:" /etc/passwd; then
		case "$dist" in
			el5 )
				useradd -N "$user"
				;;
			el* )
				useradd -n "$user"
				;;
			* )
				useradd "$user"
		esac
	fi
}

# Adds specified group if it doesn't exist already
add_group() {
	local group
	group="$1"

	if ! grep -q "^$group:" /etc/group; then
		groupadd "$group"
	fi
}

# Adds user to the specified groups
add_to_groups() {
	local user
	user="$1"

	shift
	for group; do
		usermod -a -G "$group" "$user"
	done
}

. "$BASEPATH/nsti/etc/nsti.py"
