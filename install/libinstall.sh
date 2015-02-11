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
    esac
    
    if [ -n "$pkg" ]; then
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

# Detect OS & set global variables for other commands to use.
# OS variables have a detailed long variable, and a "more useful" short one:
# distro/dist, version/ver, architecture/arch. If in doubt, use the short one.
set_os_info() {
    if [ `uname -s` != "Linux" ]; then
        error "Unsupported OS detected. Can currently only detects" \
            "Linux distributions."
    fi

    if which lsb_release &>/dev/null; then
        distro=`lsb_release -si`
        version=`lsb_release -sr`
    elif [ -r /etc/redhat-release ]; then

        if is_installed centos-release; then
            distro=CentOS
        elif is_installed sl-release; then
            distro=Scientific
        elif is_installed fedora-release; then
            distro=Fedora
        elif is_installed redhat-release || is_installed redhat-release-server; then
            distro=RedHatEnterpriseServer
        fi

        version=`sed 's/.*release \([0-9.]\+\).*/\1/' /etc/redhat-release`

    else
        error "Could not determine OS. Please make sure lsb_release" \
            "is installed."
    fi

    ver="${version%%.*}"

    case "$distro" in
        CentOS | RedHatEnterpriseServer )
            dist="el$ver"
            ;;
        Debian )
            dist="debian$ver"
            ;;
        * )
            dist=$(echo "$distro$version" | tr A-Z a-z)
    esac

    architecture=`uname -m`

    # i386 is a more useful value than i686 for el5, because repo paths and
    # package names use i386
    if [ "$dist $architecture" = "el5 i686" ]; then
        arch="i386"
    else
        arch="$architecture"
    fi

    httpd='httpd'
    mysql='mysql'
    mysqld-server='mysqld-server'

    apacheuser='apache'
    apachegroup='apache'
    nagiosuser='nagios'
    nagiosgroup='nagios'
    nagioscmdgroup='nagcmd'

    phpini='/etc/php.ini'
    phpconfd='/etc/php.d'
    php_extension_dir='/usr/lib/php/modules'
    httpdconfdir='/etc/httpd/conf.d'
    mrtgcfg='/etc/mrtg/mrtg.cfg'

    case "$dist" in
        el5 | el6 | el7 )
            if [ "$arch" = "x86_64" ]; then
                php_extension_dir="/usr/lib64/php/modules"
            fi
            if [ "$dist" == "el7" ]; then
                mysql="mariadb"
            fi
            ;;
        debian6 )
            apacheuser="www-data"
            apachegroup="www-data"
            httpdconfdir="/etc/apache2/conf.d"
            mrtgcfg="/etc/mrtg.cfg"
            phpini="/etc/php5/apache2/php.ini"
            phpconfd="/etc/php5/conf.d"
            php_extension_dir="/usr/lib/php5/20090626"
            httpd="apache2"
            mysqld="mysql"
    esac
}

# Initialize installation - run basic checks and detect OS info
set_os_info 