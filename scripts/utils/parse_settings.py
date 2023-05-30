def config_to_settings(path):
    # Returns settings dict from BirdNET-pi text format.
    # Consider refactoring to use ConfigParser files, or another standardized format.
    settings = {}
    with open(path, 'r') as f:
        this_run = f.readlines()
    for i in this_run:
        key = i.split("=")[0]
        value = "=".join(i.split("=")[1:])[:-1]
        # Trim for strings, not ideal
        if value.startswith('"') and value.endswith('"'):
            value = value[1:-1]
        if value.startswith("'") and value.endswith("'"):
            value = value[1:-1]
        settings[key] = value
    return settings
