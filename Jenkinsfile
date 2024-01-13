pipeline {
    agent any
    
    stages {
        // stage('Checkout') {
        //     steps {
        //         // Haal de nieuwste code op van de Git-repository (main branch)
        //         checkout scm
        //     }
        // }
        
        stage('SonarQube Analysis') {
            environment {
                scannerHome = tool 'sonarscanner'; // Controleer of 'sonarscanner' in Jenkins is geconfigureerd
                projectName = 'itvb23ows-starter-code'
            }
            steps {
                // Voer de SonarQube-scanner uit
                script {
                    withSonarQubeEnv() {
                        sh '''
                            $scannerHome/bin/sonar-scanner \
                            -Dsonar.projectKey=$projectName
                        '''
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
