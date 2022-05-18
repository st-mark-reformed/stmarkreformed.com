set -gx PATH /opt/homebrew/bin $PATH;
set -gx PATH /usr/local/bin $PATH
set -gx PATH /usr/local/opt/ruby/bin $PATH

function ls
    exa -aghb
end

function ll
    exa -aghlb --group-directories-first --git
end
