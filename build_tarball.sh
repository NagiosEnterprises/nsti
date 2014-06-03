TAG=$1
git archive --format tar --prefix nsti/ --output nsti-${TAG}.tar ${TAG}^
