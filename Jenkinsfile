pipeline {
    agent any
    
    stages {
        stage('Checkout') {
            steps {
                // Haal de nieuwste code op van de Git-repository (main branch)
                checkout scm
            }
        }
        
        stage('SonarQube Analysis') {
            steps {
                // Voer de SonarQube-scanner uit
                script {
                    def scannerHome = tool 'sonarqubedocker'; // Controleer of 'sonarscanner' in Jenkins is geconfigureerd
                    withSonarQubeEnv() {
                        sh "${scannerHome}/bin/sonar-scanner"
                    }
                }
            }
        }
        
        stage("SonarQube Quality Gate") {
            steps {
                // Wacht op de kwaliteitsgate van SonarQube
                timeout(time: 1, unit: 'HOURS') {
                    waitForQualityGate abortPipeline: true
                }
            }
        }
        
        stage('Build Docker Image') {
            when {
                expression {
                    currentBuild.resultIsBetterOrEqualTo('SUCCESS')
                }
            }
            steps {
                // Voer hier de stappen uit om een Docker-image te bouwen
                sh 'docker build -t hive-final:latest .'
            }
        }
    }
}
