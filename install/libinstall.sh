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

# Adds the specified repo to the system package manager. Can be one of:
#     rpmforge, epel, cr (centos' continuous release repo)
add_yum_repo() {
    local repo url pkg
    repo="$1"

    # See if we need to install the repo...
    if is_installed "$repo-release"; then
        echo "$repo-release RPM installed OK"
        return 0
    fi

    echo "Enabling $repo repo..."

    case "$repo" in
        rpmforge )
            pkg=$(curl -s --connect-timeout 60 http://repoforge.org/use/ | grep -o "rpmforge-release-[0-9.-]\+\.$dist\.rf\.$arch\.rpm")
            url="http://pkgs.repoforge.org/rpmforge-release/$pkg"
            ;;
        epel )
            pkg=$(curl -s --connect-timeout 60 "http://dl.fedoraproject.org/pub/epel/$ver/i386/repoview/epel-release.html" | grep -o "epel-release-[0-9.-]\+\.noarch\.rpm")
            url="http://dl.fedoraproject.org/pub/epel/$ver/i386/$pkg"
            ;;
        cr )
            if [ "$dist"] = "el6" ] && is_installed centos-release; then
                yum -y install centos-release-cr
            fi
    esac
    
    if [ -n "$url" ] && [ -n "$pkg" ]; then
        curl -L -O --connect-timeout 60 "$url"
        rpm -Uvh "$pkg"
        rm "$pkg"
    fi

    yum check-update || true

    # Check to make sure RPM was installed
    if is_installed "$repo-release"; then
        echo "$repo-release RPM installed OK"
    else
        error "$repo-release RPM was not installed - exiting."
    fi
} 


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